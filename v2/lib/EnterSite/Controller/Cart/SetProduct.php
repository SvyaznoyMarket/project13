<?php

namespace EnterSite\Controller\Cart;

use Enter\Http;
use Enter\Util\JsonDecoderTrait;
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
    use JsonDecoderTrait;

    public function execute(Http\Request $request) {
        $response = new Http\JsonResponse();

        $productId = (new Repository\Product())->getIdByHttpRequest($request);
        if (!$productId) {
            $response->statusCode = Http\Response::STATUS_BAD_REQUEST;

            return $response;
        }

        // TODO: похоже, придется модель описывать
        $productData = $this->jsonToArray($request->getContent());
        $productData['name'] = 'ok';

        return new Http\JsonResponse([
            'result' => $productData, // TODO: вынести на уровень JsonPage.result
        ]);
    }
}