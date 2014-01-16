<?php

namespace EnterSite;

use Enter\Curl\Client;
use Enter\Curl\Config;

trait CurlClientTrait {
    /**
     * @return Client
     */
    public function getCurlClient() {
        if (!isset($GLOBALS[__METHOD__])) {
            $config = new Config();
            $GLOBALS[__METHOD__] = new Client($config);
        }

        return $GLOBALS[__METHOD__];
    }
}