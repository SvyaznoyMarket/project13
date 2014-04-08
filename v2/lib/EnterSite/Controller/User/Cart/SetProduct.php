<?php

namespace EnterSite\Controller\User\Cart;

use Enter\Http;
use Enter\Util\JsonDecoderTrait;
use EnterSite\ConfigTrait;
use EnterSite\CurlClientTrait;
use EnterSite\LoggerTrait;
use EnterSite\Controller;
use EnterSite\Curl\Query;
use EnterSite\Model;
//use EnterSite\Model\JsonPage as Page;
use EnterSite\Repository;

class SetProduct {
    use ConfigTrait;
    use LoggerTrait, CurlClientTrait {
        ConfigTrait::getConfig insteadof LoggerTrait, CurlClientTrait;
        LoggerTrait::getLogger insteadof CurlClientTrait;
    }
    use JsonDecoderTrait;

    /**
     * @param Http\Request $request
     * @return Http\JsonResponse
     */
    public function execute(Http\Request $request) {
        $productData = array_merge([
            'id'       => null,
            'quantity' => null,
        ], (array)$request->data['product']);

        $product = new Model\Product();
        $product->id = $productData['id'];

        $cartProduct = new Model\Cart\Product();

        return new Http\JsonResponse([
            'result' => (new Repository\Partial\Cart\ProductButton())->getObject($product, $cartProduct), // TODO: вынести на уровень JsonPage.result
        ]);
    }
}