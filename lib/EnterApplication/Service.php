<?php

namespace EnterApplication;

class Service
{
    /** @var \EnterLab\Curl\Client|null */
    protected $curl;

    /**
     * @return \EnterLab\Curl\Client
     */
    public function getCurl()
    {
        if (!$this->curl) {
            $config = new \EnterLab\Curl\Config();
            $config->defaultQueryTimeout = \App::config()->coreV2['timeout'];

            $this->curl = new \EnterLab\Curl\Client($config);
        }

        return $this->curl;
    }

    /**
     * @return void
     */
    public function removeCurl()
    {
        if ($this->curl) {
            $this->curl->__destruct();
            $this->curl = null;
        }
    }
}