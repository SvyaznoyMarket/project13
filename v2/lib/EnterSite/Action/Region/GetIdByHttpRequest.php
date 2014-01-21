<?php

namespace EnterSite\Action\Region;

use Enter\Http;
use EnterSite\ConfigTrait;

class GetIdByHttpRequest {
    use ConfigTrait;

    /**
     * @param Http\Request $request
     * @return int
     */
    public function execute(Http\Request $request) {
        $config = $this->getConfig()->region;

        $id = (int)$request->cookie[$config->cookieName] ?: $config->defaultId;

        return $id;
    }
}