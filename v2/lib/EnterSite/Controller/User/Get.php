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

        // ид региона
        $regionId = (new Repository\Region())->getIdByHttpRequestCookie($request);

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

        // страница
        $page = new Page();

        $page->user->sessionId = $session->getId();

        $userBlock = (new Repository\Partial\UserBlock())->getObject($cart, $user);
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

        $response = new Http\JsonResponse([
            'result' => $page,
        ]);

        // информационная кука пользователя
        // TODO: вынести в Action\HandleResponse
        $needCookie = (bool)$cart->product || $user;
        $changeCookie = false
            || !isset($request->cookies[$config->userToken->infoCookieName])
            || ((bool)$request->cookies[$config->userToken->infoCookieName] && !$needCookie)
            || (!(bool)$request->cookies[$config->userToken->infoCookieName] && $needCookie)
        ;
        if ($changeCookie) {
            $response->headers->setCookie(new Http\Cookie($config->userToken->infoCookieName, $needCookie ? 1: 0, time() + $config->session->cookieLifetime, '/', null, false, false));
        }

        return $response;
    }
}