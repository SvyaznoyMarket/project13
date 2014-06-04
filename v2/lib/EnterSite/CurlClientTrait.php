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
            $globalConfig = $this->getConfig();

            $config = new Curl\Config();
            $config->encoding = 'gzip,deflate'; // важно!
            $config->httpheader = ['X-Request-Id: ' . $globalConfig->requestId, 'Expect:'];
            $config->retryTimeout = $globalConfig->curl->retryTimeout;
            $config->retryCount = $globalConfig->curl->retryCount;

            $instance = new Curl\Client($config);
            $instance->setLogger($this->getLogger());

            $GLOBALS[__METHOD__] = $instance;
        }

        return $GLOBALS[__METHOD__];
    }
}