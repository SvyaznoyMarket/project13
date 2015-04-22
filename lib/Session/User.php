<?php

namespace Session;

class User {

    /** Название авторизационного токена
     * @var string
     */
    private $authTokenName;
    /** Сессионный токен
     * @var string|null
     */
    private $sessionToken;
    /** @var \Model\User\Entity */
    private $entity;
    /** @var \Model\Region\Entity */
    private $region;
    /** @var Cart */
    private $cart;
    /** @var Cart\OneClick */
    private $oneClickCart;

    public function __construct() {
        $this->authTokenName = \App::config()->authToken['name'];
        $this->sessionToken = \App::request()->cookies->get($this->authTokenName);
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
        if (!$this->sessionToken) {
            return null;
        }

        if (!$this->entity) {
            try {
                if (!$user = \RepositoryManager::user()->getEntityByToken($this->sessionToken)) {
                    $this->removeToken();
                    return null;
                }
                $user->setToken($this->sessionToken);
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

        $cookie = new \Http\Cookie(
            $this->authTokenName,
            $token,
            time() + \App::config()->session['cookie_lifetime'],
            '/',
            \App::config()->session['cookie_domain'],
            false,
            true // важно httpOnly=true, чтобы js не мог получить куку
        );
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

        $this->sessionToken = $token;
    }

    /**
     * Удаляет токен авторизации из cookie
     *
     * @param \Http\Response|\Http\RedirectResponse|null $response
     * @return null|string
     */
    public function removeToken($response = null) {
        $token = $this->getToken();

        $domainParts = explode('.', \App::config()->mainHost);
        $tld = array_pop($domainParts);
        $domain = array_pop($domainParts);
        $subdomain = array_pop($domainParts);

        if ($response) {
            $response->headers->clearCookie($this->authTokenName, '/', "$domain.$tld");
            $response->headers->clearCookie($this->authTokenName, '/', "$subdomain.$domain.$tld");
            $response->headers->clearCookie(\App::config()->authToken['authorized_cookie'], '/', "$domain.$tld");
            $response->headers->clearCookie(\App::config()->authToken['authorized_cookie'], '/', "$subdomain.$domain.$tld");
        }

        return $token;
    }

    /**
     * @return string|null
     */
    public function getToken() {
        return $this->sessionToken;
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
            $this->region = \RepositoryManager::region()->getEntityById($regionId);
            if (!$this->region) {
                \App::logger()->warn(sprintf('Регион #"%s" не найден.', $regionId), ['session', 'user']);
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
     * @return int
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
     * @return Cart\OneClick
     */
    public function getOneClickCart() {
        if (!$this->oneClickCart) {
            $this->oneClickCart = new Cart\OneClick();
        }

        return $this->oneClickCart;
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