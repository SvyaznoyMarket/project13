<?php

namespace EnterLab\Application;

trait CurlTrait {
    /**
     * @return \EnterLab\Curl\Client
     */
    protected function getCurl() {
        /** @var \EnterLab\Application\Service $service */
        $service = $GLOBALS['enter/service'];

        return $service->getCurl();
    }
}