<?php

namespace EnterSite;

use Enter\Curl;

trait CurlClientTrait {
    /**
     * @return Curl\Client
     */
    public function getCurlClient() {
        if (!isset($GLOBALS[__METHOD__])) {
            $config = new Curl\Config();
            $GLOBALS[__METHOD__] = new Curl\Client($config);
        }

        return $GLOBALS[__METHOD__];
    }
}