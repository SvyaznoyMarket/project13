<?php

namespace EnterTerminal\Controller;

use Enter\Http;
use Enter\Util\JsonDecoderTrait;
use EnterSite\ConfigTrait;
use EnterSite\CurlClientTrait;
use EnterSite\LoggerTrait;
use EnterSite\SessionTrait;
use EnterSite\Curl\Query;
use EnterSite\Model;
use EnterSite\Repository;
use EnterTerminal\Model\Page\Cart as Page;

class Cart {
    use ConfigTrait, LoggerTrait, CurlClientTrait, SessionTrait {
        ConfigTrait::getConfig insteadof LoggerTrait, CurlClientTrait, SessionTrait;
        LoggerTrait::getLogger insteadof CurlClientTrait, SessionTrait;
    }

    /**
     * @param Http\Request $request
     * @throws \Exception
     * @return Http\JsonResponse
     */
    public function execute(Http\Request $request) {
        $config = $this->getConfig();
        $session = $this->getSession();
        $curl = $this->getCurlClient();
        $cartRepository = new Repository\Cart();

        // ид магазина
        $shopId = (new \EnterTerminal\Repository\Shop())->getIdByHttpRequest($request); // FIXME

        // запрос магазина
        $shopItemQuery = new Query\Shop\GetItemById($shopId);
        $curl->prepare($shopItemQuery);

        $curl->execute();

        // магазин
        $shop = (new Repository\Shop())->getObjectByQuery($shopItemQuery);
        if (!$shop) {
            throw new \Exception(sprintf('Магазин #%s не найден', $shopId));
        }

        // корзина из сессии
        $cart = $cartRepository->getObjectByHttpSession($session);

        $productsById = [];
        foreach ($cart->product as $cartProduct) {
            $productsById[$cartProduct->id] = null;
        }

        $productListQuery = null;
        if ((bool)$productsById) {
            $productListQuery = new Query\Product\GetListByIdList(array_keys($productsById), $shop->regionId);
            $curl->prepare($productListQuery);
        }

        $cartItemQuery = new Query\Cart\GetItem($cart, $shop->regionId);
        $curl->prepare($cartItemQuery);

        $curl->execute();

        if ($productListQuery) {
            $productsById = (new Repository\Product())->getIndexedObjectListByQueryList([$productListQuery]);
        }

        // корзина из ядра
        $cart = $cartRepository->getObjectByQuery($cartItemQuery);

        // страница
        $page = new Page();

        $page->sum = $cart->sum;

        foreach (array_reverse($cart->product) as $cartProduct) {
            $product = !empty($productsById[$cartProduct->id])
                ? $productsById[$cartProduct->id]
                : new Model\Product([
                    'id' => $cartProduct->id,
                ]);

            $product->quantity = $cartProduct->quantity; // FIXME
            $product->sum = $cartProduct->sum; // FIXME

            $page->products[] = $product;
        }

        // response
        return new Http\JsonResponse($page);
    }
}