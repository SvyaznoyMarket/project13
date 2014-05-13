<?php

namespace EnterTerminal\Repository;

use Enter\Http;
use EnterSite\Repository\Shop as BaseRepository;

class Shop extends BaseRepository {
    /**
     * @param Http\Request $request
     * @return string|null
     */
    public function getIdByHttpRequest(Http\Request $request) {
        return is_scalar($request->query['shopId']) ? (string)$request->query['shopId'] : null;
    }
}