<?php

namespace Payment;

class SvyaznoyClubManager {
    private $cookieLifetime;
    private $params4get = [];

    public function __construct() {
        $userTicket = \App::config()->svyaznoyClub['userTicket']['cookieName'];
        $cardNumber = \App::config()->svyaznoyClub['cardNumber']['cookieName'];

        $this->params4get = array_merge($this->params4get, [$userTicket, $cardNumber]);
        $this->cookieLifetime = \App::config()->svyaznoyClub['cookieLifetime'];
    }

    /**
     * @param \Http\Response $response
     */
    public function set(\Http\Response $response = null) {
        try {
            $request = \App::request();
            $cookie = null;

            $getParams = [];
            foreach( $this->params4get as $param ){
                $getParams[$param] = $request->get($param) ?: '';
            }

            foreach( $getParams as $key => $value ){
                if (!empty($value)) {
                    $response->headers->setCookie(new \Http\Cookie(
                        $key,
                        $value, time() + $this->cookieLifetime, '/', null, false, true
                    ));
                }
            }
        } catch (\Exception $e) {
            \App::logger()->error($e, ['svyaznoyClub']);
        }
    }
} 