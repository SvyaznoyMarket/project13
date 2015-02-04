<?php

namespace EnterLab\Application;

class Service
{
    /**
     * @return \EnterLab\Curl\Client
     */
    public function getCurl()
    {
        static $instance;

        if (!$instance) {
            $config = new \EnterLab\Curl\Config();
            $config->defaultQueryTimeout = \App::config()->coreV2['timeout'];

            $instance = new \EnterLab\Curl\Client($config);
        }

        return $instance;
    }
}