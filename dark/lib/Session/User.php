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
     * @return \Model\User\Entity|null
     */
    public function getEntity() {
        if (!$this->token) {
            return null;
        }

        if (!$this->entity) {
            $user = \RepositoryManager::getUser()->getEntityByToken($this->token);
            $user->setToken($this->token);
            if (!$user) {
                return null;
            }

            $this->entity = $user;
        }

        return $this->entity;
    }

    /**
     * @param \Model\User\Entity $user
     */
    public function signIn(\Model\User\Entity $user, \Http\Response $response) {
        $user->setIpAddress(\App::request()->getClientIp());
        $this->setToken($user->getToken());
        //\RepositoryManager::getUser()->saveEntity($user);

        $this->setCacheCookie($response);
    }

    /**
     * @param string $token
     */
    public function setToken($token) {
        if (!$token) {
            throw new \LogicException('Токен пользователя не должен быть пустым.');
        }

        \App::session()->set($this->tokenName, $token);
    }

    public function removeToken() {
        \App::session()->remove($this->tokenName);
    }

    /**
     * @return string
     */
    public function getToken() {
        return \App::session()->get($this->tokenName, null);
    }

    public function setRegion(\Model\Region\Entity $region, \Http\Response $response) {
        if (!$region->getId()) {
            throw new \LogicException('Ид региона не должен быть пустым.');
        }

        $cookie = new \Http\Cookie(\App::config()->region['cookieName'], $region->getId(), time() + \App::config()->region['cookieLifetime']);
        $response->headers->setCookie($cookie);
    }

    /**
     * @return \Model\Region\Entity|null
     * @throws \RuntimeException
     */
    public function getRegion() {
        if (!$this->region) {
            $cookieName = \App::config()->region['cookieName'];

            if (\App::request()->cookies->has($cookieName)) {
                $id = (int)\App::request()->cookies->get($cookieName);
                $this->region = \RepositoryManager::getRegion()->getEntityById($id);
                if (!$this->region) {
                    \App::logger()->warn(sprintf('Регион #"%s" не найден.', $cookieName));
                }
            }
        }

        if (!$this->region) {
            $this->region = \RepositoryManager::getRegion()->getDefaultEntity(\App::config()->region['defaultId']);
        }

        if (!$this->region) {
            throw new \RuntimeException('Не удалось получить регион.');
        }

        return $this->region;
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
        $cookie = new \Http\Cookie('enter_auth', $value);
        \App::logger()->debug(sprintf('Cache cookie %s cooked', $value));

        $response->headers->setCookie($cookie);
    }
}