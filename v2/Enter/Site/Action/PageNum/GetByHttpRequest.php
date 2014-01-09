<?php

namespace Enter\Site\Action\PageNum;

use Enter\Http\Request;

class GetByHttpRequest {
    /**
     * @param Request $request
     * @return int
     */
    public function execute(Request $request) {
        $pageNum = (int)$request->query['page'] ?: 1;

        return $pageNum;
    }
}