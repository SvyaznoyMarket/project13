<?php

namespace EnterSite;

use Enter\Curl;

trait CurlClientTrait {
    use LoggerTrait;

    /**
     * @return Curl\Client
     */
    public function getCurlClient() {
        if (!isset($GLOBALS[__METHOD__])) {
            $config = new Curl\Config();

            $instance = new Curl\Client($config);
            $instance->setLogger($this->getLogger());

            $GLOBALS[__METHOD__] = $instance;
        }

        return $GLOBALS[__METHOD__];
    }
}