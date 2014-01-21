<?php

namespace EnterSite\Action\Product\Category;

use Enter\Http;

class GetTokenByHttpRequest {
    /**
     * @param Http\Request $request
     * @return string
     */
    public function execute(Http\Request $request) {
        $token = explode('/', $request->query['productCategoryPath']);
        $token = end($token);

        return $token;
    }
}