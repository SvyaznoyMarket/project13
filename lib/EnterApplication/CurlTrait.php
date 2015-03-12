<?php

namespace EnterApplication;

trait CurlTrait {
    /**
     * @return \EnterLab\Curl\Client
     */
    protected function getCurl()
    {
        /** @var \EnterApplication\Service $service */
        $service = $GLOBALS['enter/service'];

        return $service->getCurl();
    }

    /**
     * @return void
     */
    protected function removeCurl()
    {
        /** @var \EnterApplication\Service $service */
        $service = $GLOBALS['enter/service'];

        $service->removeCurl();
    }
}