<?php

namespace EnterSite\Controller\User\Cart;

use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\CurlClientTrait;
use EnterSite\LoggerTrait;
use EnterSite\SessionTrait;
use EnterSite\Curl\Query;
use EnterSite\Model;
use EnterSite\Repository;
use EnterSite\Model\Page\User\Cart\SetProduct as Page;

class SetProduct {
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

        $productData = array_merge([
            'id'       => null,
            'quantity' => null,
        ], (array)$request->data['product']);

        $product = new Model\Product();
        $product->id = $productData['id'];

        $cartProduct = new Model\Cart\Product();
        $cartProduct->id = $productData['id'];
        $cartProduct->quantity = (int)$productData['quantity'];

        $page = new Page();
        $page->buyButton = (new Repository\Partial\Cart\ProductButton())->getObject($product, $cartProduct);
        $page->buySpinner = (new Repository\Partial\Cart\ProductSpinner())->getObject($product, $cartProduct);

        // TODO вынести в класс корзины
        $session = $this->getSession();
        $cartData = $session->get('userCart', [
            'productList' => [],
        ]);
        $cartData['productList'][$cartProduct->id] = $cartProduct->quantity;
        $session->set('userCart', $cartData);

        // response
        $response = new Http\JsonResponse([
            'result' => $page,
        ]);

        // информационная кука пользователя
        $response->headers->setCookie(new Http\Cookie($config->userToken->infoCookieName, 1, time() + $config->session->cookieLifetime, '/', null, false, false));

        // TODO: вынести на уровень JsonPage.result
        return $response;
    }
}