<?php

namespace EnterSite\Controller\Cart;

use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\Controller;
use EnterSite\Model\JsonPage as Page;
use EnterSite\Repository;

class SetProduct {
    use ConfigTrait;
    use LoggerTrait {
        ConfigTrait::getConfig insteadof LoggerTrait;
    }

    public function execute(Http\Request $request) {
        $response = new Http\JsonResponse();

        $productId = (new Repository\Product())->getIdByHttpRequest($request);
        if (!$productId) {
            $response->statusCode = Http\Response::STATUS_BAD_REQUEST;

            return $response;
        }

        // FIXME: заглушка
        return new Http\JsonResponse([
            'success' => true,
        ]);
    }
}