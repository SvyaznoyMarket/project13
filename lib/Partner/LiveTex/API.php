<?php
/*
 *
 * Можно также добавить "кешировние" — записть в сессию полученных от апи результатов,
 * чтобы уменьшить кол-во обращений к АПИ.
 * Пока добавлено кешированине в сессию chief_id и authKey дабы не запрашивать при перезагрузке страницы авторизацию
 *
 */

namespace Partner\LiveTex;


class API {

    private static $instance = null;
    private $authKey = null;
    private $chief_id = null;
    private $login = null;
    private $password = null;
    private $api_url = 'http://api.livetex.ru/';
    private $api_login_url = 'http://api.livetex.ru/login.php';


    private function __clone() {}
    private function __construct($log, $pass) {
        $this->login = $log;
        $this->password = $pass;
    }


    public static function getInstance($log, $pass)
    {
        if (null === self::$instance)
        {
            self::$instance = new self($log, $pass);
        }
        return self::$instance;
    }


    private function curl($url, $post_arr) {
        if( $curl = curl_init() ) {
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post_arr );
            $out = curl_exec($curl);
            curl_close($curl);
            return json_decode($out);
        }
        return false;
    }



    public function tologin() {
        $post_arr = [
            'login' => $this->login,
            'password' => $this->password,
        ];

        $response = $this->curl( $this->api_login_url, $post_arr );

        if (isset($response->response) and $response->response) {
            if ( isset($response->response->authkey) ) $this->authKey = $response->response->authkey;
                else $this->authKey = false;

            if ( isset($response->response->chief_id) ) $this->chief_id = $response->response->chief_id;
                else $this->chief_id = false;

            $this->setSession();
        }

        return $response;
    }


    public function tologout() {
        $this->unsetSession();
    }


    public function method( $method, $data = [] ) {
        if ( $this->authKey == null || $this->chief_id == null ) {
            $this->getSession();
            if ( $this->authKey == null || $this->chief_id == null ) {
                $this->tologin();
            }
        }

        $post_arr = [
            'chiefId' => $this->chief_id,
            'authKey' => $this->authKey,
            'protocol' => 'json',
            'method' => $method,
            'data' => json_encode($data),
        ];

        $response = $this->curl( $this->api_url, $post_arr );


        $error = $response->error;
        if ( $error ) {
            $this->unsetSession();
            //return $this->method($method, $data);
        }


        return $response;

    }


    /*
    public function testmethod( $method, $data = [] ) {
        if ( $this->authKey == null || $this->chief_id == null ) {           
            $this->getSession();
            if ( $this->authKey == null || $this->chief_id == null ) {
                $this->tologin();   
            }
        }

        $post_arr =
            "chiefId=".$this->chief_id.
            "&authKey=".$this->authKey.
            "&protocol=json&method=".$method;

        foreach($data as $key => $val) {
            $post_arr .= "&data[".$key."]=".$val;
        }

        $response = $this->curl( $this->api_url, $post_arr );

        return $response;

    }
    */



    private function setSession() {
        \App::session()->set( 'ltstat_authKey', $this->authKey );
        \App::session()->set( 'ltstat_chief_id', $this->chief_id );
    }


    private function getSession() {
        $this->authKey = \App::session()->get( 'ltstat_authKey');
        $this->chief_id = \App::session()->get( 'ltstat_chief_id');
    }


    private function unsetSession() {
        \App::session()->remove('ltstat_authKey');
        \App::session()->remove('ltstat_chief_id');
    }


}