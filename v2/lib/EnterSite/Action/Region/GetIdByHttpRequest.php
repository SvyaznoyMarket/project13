<?php

namespace EnterSite\Action\Region;

use EnterSite\ConfigTrait;
use Enter\Http\Request;

class GetIdByHttpRequest {
    use ConfigTrait;

    /**
     * @param Request $request
     * @return int
     */
    public function execute(Request $request) {
        $config = $this->getConfig()->region;

        $id = (int)$request->cookie[$config->cookieName] ?: $config->defaultId;

        return $id;
    }
}