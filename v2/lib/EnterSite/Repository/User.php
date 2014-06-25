<?php

namespace EnterSite\Repository;

use Enter\Http;
use Enter\Curl\Query;
use EnterSite\ConfigTrait;
use EnterSite\Model;

class User {
    use ConfigTrait;

    /**
     * @param Http\Session $session
     * @return string|null
     */
    public function getTokenByHttpSession(Http\Session $session) {
        return $session->get($this->getConfig()->userToken->authCookieName);
    }

    /**
     * @param Query $query
     * @return Model\User|null
     */
    public function getObjectByQuery(Query $query) {
        $user = null;

        if ($item = $query->getResult()) {
            $user = new Model\User($item);
        }

        return $user;
    }
}