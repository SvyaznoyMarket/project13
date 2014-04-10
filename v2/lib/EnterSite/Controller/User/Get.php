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
        $session = $this->getSession();
        $curl = $this->getCurlClient();
        $cartRepository = new Repository\Cart();

        $page = new Page();

        $regionId = (new Repository\Region())->getIdByHttpRequest($request);

        // корзина из сессии
        $cart = $cartRepository->getObjectByHttpSession($session);

        $cartItemQuery = new Query\Cart\GetItem($cart, $regionId);
        $curl->prepare($cartItemQuery);

        $curl->execute(1, 2);

        // корзина из ядра
        $cart = $cartRepository->getObjectByQuery($cartItemQuery);

        // TODO: загрузка товаров

        foreach ($cart->product as $cartProduct) {
            $product = new Model\Product([
                'id' => $cartProduct->id,
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