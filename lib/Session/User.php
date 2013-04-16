<?php

namespace Session;

class User {
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

    public function __construct() {
        $this->tokenName = \App::config()->authToken['name'];
        $this->token = \App::session()->get($this->tokenName);
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
                    \App::logger()->warn(sprintf('Регион #"%s" не найден.', $regionId));
                }
            }
        }

        if (!$this->region) {
            if ('terminal' == \App::$name) {
                $shop = \RepositoryManager::shop()->getEntityById(\App::config()->region['shop_id']);
                if (!$shop) {
                    \App::logger()->warn(sprintf('Магазин #"%s" не найден.', \App::config()->region['shop_id']));
                    $this->region = \RepositoryManager::region()->getDefaultEntity(\App::config()->region['defaultId']);
                } else {
                    $this->region = \RepositoryManager::region()->getEntityById($shop->getRegion()->getId());
                    if (!$this->region) {
                        \App::logger()->warn(sprintf('Регион #"%s" не найден.', $regionId));
                        $this->region = \RepositoryManager::region()->getDefaultEntity(\App::config()->region['defaultId']);
                    }
                }
            } else {
                $this->region = \RepositoryManager::region()->getDefaultEntity(\App::config()->region['defaultId']);
            }
        }

        if (!$this->region) {
            throw new \RuntimeException('Не удалось получить регион.');
        }

        return $this->region;
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
        \App::logger()->debug(sprintf('Cache cookie %s cooked', $value));

        $response->headers->setCookie($cookie);
    }
}