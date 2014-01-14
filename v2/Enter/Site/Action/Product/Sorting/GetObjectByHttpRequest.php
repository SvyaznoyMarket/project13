<?php

namespace Enter\Site\Action\Product\Sorting;

use Enter\Http\Request;
use Enter\Site\Model\Product\Sorting;

class GetObjectByHttpRequest {
    /**
     * @param Request $request
     * @return Sorting|null
     */
    public function execute(Request $request) {
        $sorting = null;

        $data = explode('-', $request->query['sort']);
        if (isset($data[0]) && isset($data[1])) {
            $sorting = new Sorting();
            $sorting->token = $data[0];
            $sorting->direction = $data[1];
        }

        return $sorting;
    }
}