<?php

namespace EnterSite\Controller\User;

use Enter\Http;
use Enter\Util\JsonDecoderTrait;
use EnterSite\ConfigTrait;
use EnterSite\CurlClientTrait;
use EnterSite\LoggerTrait;
use EnterSite\SessionTrait;
use EnterSite\Curl\Query;
use EnterSite\Model;
use EnterSite\Repository;
use EnterSite\Model\Page\User\Get as Page;

class Get {
    use ConfigTrait;
    use LoggerTrait, CurlClientTrait, SessionTrait {
        ConfigTrait::getConfig insteadof LoggerTrait, CurlClientTrait, SessionTrait;
        LoggerTrait::getLogger insteadof CurlClientTrait, SessionTrait;
    }

    /**
     * @param Http\Request $request
     * @return Http\JsonResponse
     */
    public function execute(Http\Request $request) {
        $config = $this->getConfig();

        $page = new Page();

        // TODO вынести в класс корзины
        $session = $this->getSession();
        $cartData = $session->get('userCart', [
            'productList' => [],
        ]);

        foreach ($cartData['productList'] as $productId => $quantity) {
            $product = new Model\Product([
                'id' => $productId,
            ]);
            $cartProduct = new Model\Cart\Product([
                'id'       => $productId,
                'quantity' => $quantity,
            ]);

            $page->buyButtons['.' . Repository\Partial\Cart\ProductButton::getId($product->id)] = (new Repository\Partial\Cart\ProductButton())->getObject($product, $cartProduct);
            $page->buySpinners['.' . Repository\Partial\Cart\ProductSpinner::getId($product->id)] = (new Repository\Partial\Cart\ProductSpinner())->getObject($product, $cartProduct);
        }

        // TODO: вынести на уровень JsonPage.result
        return new Http\JsonResponse([
            'result' => $page,
        ]);
    }
}