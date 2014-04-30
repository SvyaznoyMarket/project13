<?php

namespace EnterSite;

use Enter\Curl;

trait CurlClientTrait {
    use ConfigTrait, LoggerTrait {
        ConfigTrait::getConfig insteadof LoggerTrait;
    }

    /**
     * @return Curl\Client
     */
    protected function getCurlClient() {
        if (!isset($GLOBALS[__METHOD__])) {
            $config = new Curl\Config();
            $config->encoding = 'gzip,deflate'; // важно!
            $config->httpheader = ['X-Request-Id: ' . $this->getConfig()->requestId, 'Expect:'];

            $instance = new Curl\Client($config);
            $instance->setLogger($this->getLogger());

            $GLOBALS[__METHOD__] = $instance;
        }

        return $GLOBALS[__METHOD__];
    }
}