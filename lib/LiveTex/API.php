<?php

namespace LiveTex;


class API {

    private static $instance = null;
    private $authKey = null;
    private $chief_id = null;

    // TODO: move in config
    private $login = 'anastasiya.vs@enter.ru';
    private $password = 'enter1chat2';

    private $api_url = 'http://api.livetex.ru/';
    private $api_login_url = 'http://api.livetex.ru/login.php';


    private function __clone() {}
    private function __construct() {}


    public static function getInstance()
    {
        if (null === self::$instance)
        {
            self::$instance = new self();
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



    public function login() {
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
        }

        return $response;
    }


    public function method( $method, $data = [] ) {
        if ( $this->authKey == null || $this->chief_id == null ) {
            $this->login();
        }

        $post_arr = [
            'chiefId' => $this->chief_id,
            'authKey' => $this->authKey,
            'protocol' => 'json',
            'method' => $method,
            'data' => json_encode($data),
        ];

        $response = $this->curl( $this->api_url, $post_arr );

        return $response;

    }


    public function testmethod( $method, $data = [] ) {
        if ( $this->authKey == null || $this->chief_id == null ) {
            $this->login();
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


}