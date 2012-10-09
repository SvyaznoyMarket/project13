<?php

namespace Session;

class User {
    /** @var string */
    private $cookieName = '_token';
    /** @var string */
    private $cookieLifetime = 151200;
    /** @var \Http\Request */
    private $request;
    /** @var \Model\User\Entity */
    private $entity;
    /** @var \Model\Region\Entity */
    private $region;
    /** @var Cart */
    private $cart;

    public function __construct() {
        $this->cookieName = \App::config()->authToken['name'];
        $this->cookieLifetime = \App::config()->authToken['lifetime'];
        $this->request = \App::request();

        if ($this->request->cookies->has($this->cookieName)) {
            $this->token = $this->request->cookies->get($this->cookieName);
        }
    }

    public function getEntity() {
        if (!$this->token) {
            return null;
        }

        if (!$this->entity) {
            $repository = \RepositoryManager::getUser();

            $user = $repository->getEntityByToken($this->token);
            if (!$user) {
                return null;
            }

            $this->entity = $user;
        }

        return $this->entity;
    }

    /**
     * @param \Model\User\Entity $user
     * @param \Http\Response     $response
     */
    public function signIn(\Model\User\Entity $user, \Http\Response $response) {
        $user->setTokenExpiredAt(new \DateTime('+' . $this->cookieLifetime . ' seconds'));
        $user->setIpAddress($this->request->getClientIp());
        $this->setToken($user->getToken(), $response);
        //\RepositoryManager::getUser()->saveEntity($user);
    }

    /**
     * @param string $token
     */
    private function setToken($token, \Http\Response $response) {
        if (!$token) {
            throw new \LogicException('Токен пользователя не должен быть пустым.');
        }

        $cookie = new \Http\Cookie($this->cookieName, $token, time() + $this->cookieLifetime);
        $response->headers->setCookie($cookie);
    }

    private function removeToken(\Http\Response $response) {
        $response->headers->clearCookie(self::$cookieName);
    }

    /**
     * @return string
     */
    private function getToken() {
        return $this->request->cookies->get(self::$cookieName);
    }

    public function setRegion(\Model\Region\Entity $region, \Http\Response $response) {
        if (!$region->getId()) {
            throw new \LogicException('Ид региона не должен быть пустым.');
        }

        $cookie = new \Http\Cookie($this->cookieName, $region->getId(), time() + \App::config()->region['cookieLifetime']);
        $response->headers->setCookie($cookie);
    }

    public function getRegion() {
        if (!$this->region) {
            $cookieName = \App::config()->region['cookieName'];

            if ($this->request->cookies->has($cookieName)) {
                $id = (int)$this->request->cookies->get($cookieName);
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

    public function getCart() {
        if (!$this->cart) {
            $this->cart = new Cart();
        }

        return $this->cart;
    }
}