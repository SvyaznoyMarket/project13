<?php

namespace Enter\Site\Action\Product\Category;

use Enter\Http\Request;

class GetTokenByHttpRequest {
    /**
     * @param Request $request
     * @return mixed
     */
    public function execute(Request $request) {
        $token = explode('/', $request->query['productCategoryPath']);
        $token = end($token);

        return $token;
    }
}