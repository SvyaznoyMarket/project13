<?php

namespace Session;

class User {

    const PRODUCT_HISTORY = 100;

    /** @var string */
    private $tokenName;
    /** @var string|null */
    private $token;
    /** @var \Http\Session */
    private $session;
    /** @var \Model\User\Entity */
    private $entity;
    /** @var \Model\Region\Entity */
    private $region;
    /** @var Cart */
    private $cart;
    /** @var Cart\LifeGift */
    private $lifeGiftCart;
    /** @var Cart\OneClick */
    private $oneClickCart;
    /** @var array */
    private $recommendedProduct;

    public function __construct() {
        $this->tokenName = \App::config()->authToken['name'];
        $this->token = \App::request()->cookies->get($this->tokenName);
        $this->recommendedProduct = \App::session()->get('recommendedProduct');
    }

    /**
     * @param \Model\User\Entity $entity
     */
    public function setEntity(\Model\User\Entity $entity) {
        $entity->setToken($this->getToken());
        $this->entity = $entity;
    }

    /**
     * @return \Model\User\Entity|null
     */
    public function getEntity() {
        if (!$this->token) {
            return null;
        }

        if (!$this->entity) {
            try {
                if (!$user = \RepositoryManager::user()->getEntityByToken($this->token)) {
                    $this->removeToken();
                    return null;
                }
                $user->setToken($this->token);
            } catch (\Exception $e) {
                $user = null;
                switch ($e->getCode()) {
                    case 402:
                        $this->removeToken();
                        \App::exception()->remove($e);
                        break;
                }
            }
            if (!(bool)$user) {
                return null;
            }

            $this->entity = $user;
        }

        return $this->entity;
    }

    /**
     * @param \Model\User\Entity $user
     * @param \Http\Response $response
     */
    public function signIn(\Model\User\Entity $user, \Http\Response $response) {
        $token = $user->getToken();

        $user->setIpAddress(\App::request()->getClientIp());
        $this->setToken($token);

        // SITE-1260 {
        $cookie = new \Http\Cookie(
            $this->tokenName,
            $token,
            time() + \App::config()->session['cookie_lifetime'],
            '/',
            \App::config()->session['cookie_domain'],
            false,
            true // важно httpOnly=true, чтобы js не мог получить куку
        );
        $response->headers->setCookie($cookie);
        // }

        self::enableInfoCookie($response); // SITE-2709

        //\RepositoryManager::getUser()->saveEntity($user);

        $this->setCacheCookie($response);

        $response->headers->setCookie($cookie);
    }

    /**
     * Устанавливает токен
     *
     * @param string $token
     * @throws \LogicException
     */
    public function setToken($token) {
        if (!$token) {
            throw new \LogicException('Токен пользователя не должен быть пустым.');
        }

        $this->token = $token;
    }

    /**
     * Удаляет токен
     *
     * @param \Http\Response|\Http\RedirectResponse|null $response
     */
    public function removeToken($response = null) {
        $token = $this->getToken();

        $domainParts = explode('.', \App::config()->mainHost);
        $tld = array_pop($domainParts);
        $domain = array_pop($domainParts);
        $subdomain = array_pop($domainParts);

        if ($response) {
            $response->headers->clearCookie($this->tokenName, '/', "$domain.$tld");
            $response->headers->clearCookie($this->tokenName, '/', "$subdomain.$domain.$tld");

            $response->headers->clearCookie(\App::config()->authToken['authorized_cookie'], '/', "$domain.$tld");
            $response->headers->clearCookie(\App::config()->authToken['authorized_cookie'], '/', "$subdomain.$domain.$tld");
        }

        return $token;
    }

    /**
     * @return string|null
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * Меняет куку региона
     *
     * @param \Model\Region\Entity $region
     * @param \Http\Response $response
     * @throws \LogicException
     */
    public function changeRegion(\Model\Region\Entity $region, \Http\Response $response) {
        $this->setRegion($region);

        $cookie = new \Http\Cookie(
            \App::config()->region['cookieName'],
            $region->getId(),
            time() + \App::config()->region['cookieLifetime'],
            '/',
            \App::config()->session['cookie_domain'],
            false,
            false // важно httpOnly=false, чтобы js мог получить куку
        );
        $response->headers->setCookie($cookie);
    }

    /**
     * @param \Model\Region\Entity $region
     * @throws \LogicException
     */
    public function setRegion(\Model\Region\Entity $region) {
        if (!$region->getId()) {
            throw new \LogicException('Ид региона не должен быть пустым.');
        }

        $this->region = $region;
    }

    /**
     * @return \Model\Region\Entity
     * @throws \RuntimeException
     */
    public function getRegion() {
        if (!$this->region) {
            $regionId = $this->getRegionId();

            if ($regionId) {
                $this->region = \RepositoryManager::region()->getEntityById($regionId);
                if (!$this->region) {
                    \App::logger()->warn(sprintf('Регион #"%s" не найден.', $regionId), ['session', 'user']);
                }
            // иначе автоопределение
            } else if (\App::config()->region['autoresolve']) {
                if (false !== strpos(\App::request()->headers->get('user-agent'), 'http://yandex.com/bots')) { // SITE-4393
                    $this->region = \RepositoryManager::region()->getDefaultEntity();
                } else {
                    $this->region = $this->getAutoresolvedRegion();
                }
            }
        }

        if (!$this->region) {
            $this->region = \RepositoryManager::region()->getDefaultEntity(\App::config()->region['defaultId']);
        }

        if (!$this->region) {
            throw new \RuntimeException('Не удалось получить регион.');
        }

        return $this->region;
    }

    /**
     * @return \Model\Region\Entity|null
     */
    public function getAutoresolvedRegion() {
        $region = null;

        if ($ip = \App::request()->getClientIp()) {
            \RepositoryManager::region()->prepareEntityByIp($ip,
                function($data) use (&$region) {
                    if ((bool)$data && !empty($data['id'])) {
                        $region = new \Model\Region\Entity($data);
                    }
                },
                function(\Exception $e) {
                    \App::exception()->remove($e);
                }
            );
            \App::coreClientV2()->execute();
        }

        return $region;
    }

    /**
     * Возвращает значение куки региона
     *
     * @return int|null
     */
    public function getRegionId() {
        $cookieName = \App::config()->region['cookieName'];

        return \App::request()->cookies->has($cookieName) ? (int)\App::request()->cookies->get($cookieName): \App::config()->region['defaultId'];
    }

    /**
     * @return Cart
     */
    public function getCart() {
        if (!$this->cart) {
            $this->cart = new Cart();
        }

        return $this->cart;
    }

    /**
     * @return Cart\LifeGift
     */
    public function getLifeGiftCart() {
        if (!$this->lifeGiftCart) {
            $this->lifeGiftCart = new Cart\LifeGift();
        }

        return $this->lifeGiftCart;
    }

    /**
     * @return Cart\OneClick
     */
    public function getOneClickCart() {
        if (!$this->oneClickCart) {
            $this->oneClickCart = new Cart\OneClick();
        }

        return $this->oneClickCart;
    }

    public function setCacheCookie(\Http\Response $response) {
        $value = md5(strval(\App::session()->getId()) . strval(time()));
        $cookie = new \Http\Cookie(\App::config()->cacheCookieName, $value);
        //\App::logger()->debug(sprintf('Cache cookie %s cooked', $value), ['session', 'user']);

        $response->headers->setCookie($cookie);
    }

    /**
     * @param int $productId
     * @param string $partnerName
     * @param string $key
     * @param $value
     */
    public function setRecommendedProductByParams($productId, $partnerName, $key, $value) {
        try {
            if (!is_array($this->recommendedProduct)) $this->recommendedProduct = [];
            if (count($this->recommendedProduct) >= self::PRODUCT_HISTORY) {
                reset($this->recommendedProduct);
                $count = (count($this->recommendedProduct) - (self::PRODUCT_HISTORY - 1));
                for ($i = 0; $i < $count; $i++) {
                    unset($this->recommendedProduct[key($this->recommendedProduct)]);
                }
            }
            if (!isset($this->recommendedProduct[$productId]) || !is_array($this->recommendedProduct[$productId])) $this->recommendedProduct[$productId] = [];
            if (!isset($this->recommendedProduct[$productId][$partnerName]) || !is_array($this->recommendedProduct[$productId][$partnerName])) $this->recommendedProduct[$productId][$partnerName] = [];
            $currentVal = false;
            if (isset($this->recommendedProduct[$productId][$partnerName][$key])) $currentVal = $this->recommendedProduct[$productId][$partnerName][$key];
            if (!$currentVal) {
                $this->recommendedProduct[$productId][$partnerName][$key] = $value;
            } else {
                switch ($key) {
                    case 'viewed_at':
                        if ((int)$currentVal < (int)$value) $this->recommendedProduct[$productId][$partnerName][$key] = (int)$value;
                        break;
                    default:
                        $this->recommendedProduct[$productId][$partnerName][$key] = $value;
                        break;
                }
            }
            \App::session()->set('recommendedProduct', $this->recommendedProduct);
        } catch (\Exception $e) {
            \App::logger()->warn(sprintf('Не удалось добавить рекоммендацию товара #"%s" от партнера #"%s".', $productId, $partnerName), ['session', 'user']);
        }
    }

    /**
     * @param int $productId
     * @param string $partnerName
     * @param string $key
     * @return bool
     */
    public function getRecommendedProductByParams($productId, $partnerName, $key) {
        try {
            return $this->recommendedProduct[$productId][$partnerName][$key];
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param int $productId
     * @param string $partnerName
     * @param string $key
     * @return bool
     */
    public function deleteRecommendedProductByParams($productId, $partnerName, $key) {
        try {
            unset($this->recommendedProduct[$productId][$partnerName][$key]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param array $recommendedProduct
     */
    public function setRecommendedProduct($recommendedProduct)
    {
        $this->recommendedProduct = $recommendedProduct;
    }

    /**
     * @return array
     */
    public function getRecommendedProduct()
    {
        return $this->recommendedProduct;
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param $response
     */
    public static function enableInfoCookie(&$response) {
        $time = time() + \App::config()->session['cookie_lifetime'];

        $cookie = new \Http\Cookie(
            \App::config()->authToken['authorized_cookie'],
            1, //cookieValue
            $time,
            '/',
            \App::config()->session['cookie_domain'],
            false,
            false
        );
        $response->headers->setCookie($cookie);

        // мобильная версия сайта
        /*
        if ($mobileHost = \App::config()->mobileHost) {
            $cookie = new \Http\Cookie(
                $cookie->getName(),
                $cookie->getValue(), //cookieValue
                $cookie->getExpiresTime(),
                $cookie->getPath(),
                $mobileHost,
                $cookie->isSecure(),
                $cookie->isHttpOnly()
            );
            $response->headers->setCookie($cookie);
        }
        */
    }

    /**
     * @param $response
     */
    public static function disableInfoCookie(&$response) {
        $time = time() + \App::config()->session['cookie_lifetime'];

        $cookie = new \Http\Cookie(
            \App::config()->authToken['authorized_cookie'],
            0, //cookieValue
            $time,
            '/',
            null,
            false,
            false
        );
        $response->headers->setCookie($cookie);

        // мобильная версия сайта
        /*
        if ($mobileHost = \App::config()->mobileHost) {
            $cookie = new \Http\Cookie(
                $cookie->getName(),
                $cookie->getValue(), //cookieValue
                $cookie->getExpiresTime(),
                $cookie->getPath(),
                $mobileHost,
                $cookie->isSecure(),
                $cookie->isHttpOnly()
            );
            $response->headers->setCookie($cookie);
        }
        */
    }


    /** Подписан пользователь на канал?
     * @param   $channelId int ID канала (по умолчанию - 1)
     * @return  bool
     */
    public function isSubscribed($channelId = 1) {
        $userEntity = \App::user()->getEntity();
        $isSubscribed = false;
        // Если мы определили юзера
        if ($userEntity) {
            $subscriptions = $userEntity->getSubscriptions();
            foreach ($subscriptions as $sub) {
                if ($sub->getChannelId() == $channelId && $sub->getIsConfirmed() == true) {
                    $isSubscribed = true;
                    break;
                }
            }
        } else {
            $subscriptionsCookie = \App::request()->cookies->get(\App::config()->subscribe['cookieName2']);
            if ($subscriptionsCookie == null) {
                return false;
            } else {
                $subscriptions = (array)json_decode($subscriptionsCookie, true);
                $isSubscribed = (bool)@$subscriptions[$channelId];
            }
        }
        return $isSubscribed;
    }
}