<?php

namespace RetailRocket;

class Manager {

    private $cookieName;
    private $cookieLifetime;

    public function __construct() {
        $this->cookieName = \App::config()->partners['RetailRocket']['userEmail']['cookieName'];
        $this->cookieLifetime = \App::config()->partners['RetailRocket']['cookieLifetime'];
    }

    public function setUserEmail(&$response, $email = null) {
        try {
            $email = trim($email);
            if (!$email) {
                throw new \Exception('Не передан email пользователя');
            }

            $response->headers->setCookie(new \Http\Cookie(
                $this->cookieName,
                $email, time() + $this->cookieLifetime, '/', null, false, true
            ));

        } catch (\Exception $e) {
            \App::exception()->remove($e);
            \App::logger()->error($e, 'RetailRocket');
        }
    }
} 