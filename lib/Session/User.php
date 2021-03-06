<?php

namespace Session;

class User {
    /** Сессионный токен
     * @var string|null
     */
    private $token;
    /** @var \Model\User\Entity */
    private $entity;
    /** @var \Model\Region\Entity */
    private $region;
    /** @var Cart */
    private $cart;

    public function __construct() {
        // TODO удалить данный блок получения токена пользователя из сookie через 1-2 месяца после релиза MSITE-637 и SITE-6685; удаление производить одновременно с подобным удалением в проекте MSITE
        $userTokenInCookie = \App::request()->cookies->get(\App::config()->authToken['name']);
        if ($userTokenInCookie) {
            \App::session()->set(\App::config()->user['tokenSessionKey'], $userTokenInCookie);
        }

        $this->token = \App::session()->get(\App::config()->user['tokenSessionKey']);
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

        \App::session()->set(\App::config()->user['tokenSessionKey'], $token);

        $cookie = new \Http\Cookie(
            \App::config()->authToken['name'],
            $token,
            time() + \App::config()->session['cookie_lifetime'],
            '/',
            \App::config()->session['cookie_domain'],
            false,
            true // важно httpOnly=true, чтобы js не мог получить куку
        );

        // TODO заменить setCookie на clearCookie через 1-2 месяца после релиза MSITE-637 и SITE-6685; замену производить одновременно с подобной заменой в проекте MSITE
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
     * Удаляет токен авторизации из cookie
     *
     * @param \Http\Response|\Http\RedirectResponse|null $response
     * @return null|string
     */
    public function removeToken($response = null) {
        $token = $this->getToken();

        \App::session()->remove(\App::config()->user['tokenSessionKey']);

        $domainParts = explode('.', \App::config()->mainHost);
        $tld = array_pop($domainParts);
        $domain = array_pop($domainParts);
        $subdomain = array_pop($domainParts);

        if ($response) {
            $response->headers->clearCookie(\App::config()->authToken['name'], '/', "$domain.$tld");
            $response->headers->clearCookie(\App::config()->authToken['name'], '/', "$subdomain.$domain.$tld");
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

        $regionId = \App::request()->cookies->has($cookieName) ? (int)\App::request()->cookies->get($cookieName) : \App::config()->region['defaultId'];
        if (2041 == $regionId) {
            $regionId = \App::config()->region['defaultId'];
        }

        return $regionId;
    }

    public function isRegionChoosed() {
        return \App::request()->cookies->has(\App::config()->region['cookieName']);
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
                if ($sub->channelId == $channelId && ($sub->isConfirmed == true)) {
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