<?php

namespace EnterSite\Repository;

use Enter\Http;

class PageNum {
    /**
     * @param Http\Request $request
     * @return int
     */
    public function getByHttpRequest(Http\Request $request) {
        $pageNum = (int)$request->query['page'] ?: 1;

        return $pageNum;
    }
}