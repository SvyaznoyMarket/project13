<?php

namespace Payment;

class SvyaznoyClubManager {
    private $cookieLifetime;
    private $params4get = ['UserTicket'];

    public function __construct() {
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