<?php

namespace EnterApplication;

trait CurlTrait {
    /**
     * @return \EnterLab\Curl\Client
     */
    protected function getCurl() {
        /** @var \EnterApplication\Service $service */
        $service = $GLOBALS['enter/service'];

        return $service->getCurl();
    }
}