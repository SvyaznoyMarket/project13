<?php

namespace EnterSite\Action\Product\Sorting;

use Enter\Http;
use EnterSite\Model;

class GetObjectByHttpRequest {
    /**
     * @param Http\Request $request
     * @return Model\Product\Sorting|null
     */
    public function execute(Http\Request $request) {
        $sorting = null;

        $data = explode('-', $request->query['sort']);
        if (isset($data[0]) && isset($data[1])) {
            $sorting = new Model\Product\Sorting();
            $sorting->token = $data[0];
            $sorting->direction = $data[1];
        }

        return $sorting;
    }
}