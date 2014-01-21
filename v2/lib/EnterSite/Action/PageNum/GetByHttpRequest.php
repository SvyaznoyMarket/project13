<?php

namespace EnterSite\Action\PageNum;

use Enter\Http;

class GetByHttpRequest {
    /**
     * @param Http\Request $request
     * @return int
     */
    public function execute(Http\Request $request) {
        $pageNum = (int)$request->query['page'] ?: 1;

        return $pageNum;
    }
}