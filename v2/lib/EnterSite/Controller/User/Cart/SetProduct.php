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
        $jsProduct = new Model\JsModel\Product($this->jsonToArray($request->getContent()));
        if (!$jsProduct->id) {
            return (new Controller\Error\NotFound())->execute($request, sprintf('Товар #%s не найден', $jsProduct->id));
        }

        $product = new Model\Product();
        $product->id = $jsProduct->id;

        $cartProduct = new Model\Cart\Product();
        $cartProduct->id = $jsProduct->id;
        $cartProduct->quantity = $jsProduct->cart ? $jsProduct->cart->quantity : 1;
        // TODO: положить товар в корзину


        if ($jsProduct->buyButton) {
            $jsProduct->buyButton->templateData = (new Repository\Partial\Cart\ProductButton())->getObject($product, $cartProduct);
        }
        $jsProduct->inCart = true;

        return new Http\JsonResponse([
            'result' => $jsProduct, // TODO: вынести на уровень JsonPage.result
        ]);
    }
}