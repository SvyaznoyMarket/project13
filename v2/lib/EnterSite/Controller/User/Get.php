<?php

namespace EnterSite\Controller\User;

use Enter\Http;
use Enter\Util\JsonDecoderTrait;
use EnterSite\ConfigTrait;
use EnterSite\CurlClientTrait;
use EnterSite\LoggerTrait;
use EnterSite\RouterTrait;
use EnterSite\SessionTrait;
use EnterSite\Curl\Query;
use EnterSite\Model;
use EnterSite\Repository;
use EnterSite\Model\Page\User\Get as Page;
use EnterSite\Routing;

class Get {
    use ConfigTrait;
    use LoggerTrait, CurlClientTrait, SessionTrait, RouterTrait {
        ConfigTrait::getConfig insteadof LoggerTrait, CurlClientTrait, SessionTrait, RouterTrait;
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
        $router = $this->getRouter();
        $cartRepository = new Repository\Cart();

        $page = new Page();

        // ид региона
        $regionId = (new Repository\Region())->getIdByHttpRequest($request);

        // корзина из сессии
        $cart = $cartRepository->getObjectByHttpSession($session);

        // токен пользователя
        $userToken = (new Repository\User)->getTokenByHttpSession($session);

        // запрос пользователя
        $userItemQuery = $userToken ? new Query\User\GetItemByToken($userToken) : null;
        if ($userItemQuery) {
            $curl->prepare($userItemQuery);
        }

        $productsById = [];
        foreach ($cart->product as $cartProduct) {
            $productsById[$cartProduct->id] = null;
        }

        $productListQuery = null;
        if ((bool)$productsById) {
            $productListQuery = new Query\Product\GetListByIdList(array_keys($productsById), $regionId);
            $curl->prepare($productListQuery);
        }

        $cartItemQuery = new Query\Cart\GetItem($cart, $regionId);
        $curl->prepare($cartItemQuery);

        $curl->execute(1, 2);

        $user = $userItemQuery ? (new Repository\User())->getObjectByQuery($userItemQuery) : null;

        if ($productListQuery) {
            $productsById = (new Repository\Product())->getIndexedObjectListByQueryList([$productListQuery]);
        }

        // корзина из ядра
        $cart = $cartRepository->getObjectByQuery($cartItemQuery);

        // сборка страницы
        $userBlock = new Model\Partial\UserBlock();
        if ($user) {
            $userBlock->isUserAuthorized = true;
            $userBlock->userLink->name = $user->firstName ?: $user->lastName;
            $userBlock->userLink->url = $router->getUrlByRoute(new Routing\User\Index());
        }

        $userBlock->isCartNotEmpty = (bool)$cart->product;
        if ($userBlock->isCartNotEmpty) {
            $userBlock->cart->url = $router->getUrlByRoute(new Routing\Cart\Index());
            $userBlock->cart->quantity = count($cart->product);
            $userBlock->cart->shownSum = $cart->sum ? number_format((float)$cart->sum, 0, ',', ' ') : null;
            $userBlock->cart->sum = $cart->sum;
        }

        $page->widgets['.' . $userBlock->widgetId] = $userBlock;

        foreach ($cart->product as $cartProduct) {
            $product = !empty($productsById[$cartProduct->id])
                ? $productsById[$cartProduct->id]
                : new Model\Product([
                    'id' => $cartProduct->id,
                ]);

            $widget = (new Repository\Partial\Cart\ProductButton())->getObject($product, $cartProduct);
            $page->widgets['.' . $widget->widgetId] = $widget;

            $widget = (new Repository\Partial\Cart\ProductSpinner())->getObject($product, $cartProduct);
            $page->widgets['.' . $widget->widgetId] = $widget;
        }

        // TODO: вынести на уровень JsonPage.result
        return new Http\JsonResponse([
            'result' => $page,
        ]);
    }
}