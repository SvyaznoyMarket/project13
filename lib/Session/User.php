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
    /** @var array */
    private $recommendedProduct;

    public function __construct() {
        $this->tokenName = \App::config()->authToken['name'];
        $this->token = \App::session()->get($this->tokenName);
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
        $user->setIpAddress(\App::request()->getClientIp());
        $this->setToken($user->getToken());
        //\RepositoryManager::getUser()->saveEntity($user);

        $this->setCacheCookie($response);
    }

    /**
     * Устанавливает токен в сессии
     *
     * @param string $token
     * @throws \LogicException
     */
    public function setToken($token) {
        if (!$token) {
            throw new \LogicException('Токен пользователя не должен быть пустым.');
        }

        \App::session()->set($this->tokenName, $token);
    }

    /**
     * Удаляет токен из сессии
     */
    public function removeToken() {
        $token = $this->getToken();
        \App::session()->remove($this->tokenName);

        return $token;
    }

    /**
     * @return string
     */
    public function getToken() {
        return \App::session()->get($this->tokenName, null);
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
            null,
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
     * @return \Model\Region\Entity|null
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
                $this->region = $this->getAutoresolvedRegion();
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
                    if ((bool)$data) {
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

        if (\App::request()->cookies->has($cookieName)) {
            return (int)\App::request()->cookies->get($cookieName);
        } else {
            return null;
        }
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

    public function setCacheCookie(\Http\Response $response) {
        $value = md5(strval(\App::session()->getId()) . strval(time()));
        $cookie = new \Http\Cookie(\App::config()->cacheCookieName, $value);
        \App::logger()->debug(sprintf('Cache cookie %s cooked', $value), ['session', 'user']);

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

}