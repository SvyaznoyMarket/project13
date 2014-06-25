<?php

namespace EnterSite\Controller\User;

use Enter\Http;
use Enter\Util\JsonDecoderTrait;
use EnterSite\ConfigTrait;
use EnterSite\CurlClientTrait;
use EnterSite\LoggerTrait;
use EnterSite\RouterTrait;
use EnterSite\SessionTrait;
use EnterSite\DebugContainerTrait;
use EnterSite\Curl\Query;
use EnterSite\Model;
use EnterSite\Repository;
use EnterSite\Model\Page\User\Get as Page;
use EnterSite\Routing;

class Get {
    use ConfigTrait, LoggerTrait, CurlClientTrait, SessionTrait, RouterTrait, DebugContainerTrait {
        ConfigTrait::getConfig insteadof LoggerTrait, CurlClientTrait, SessionTrait, RouterTrait, DebugContainerTrait;
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
        if ((bool)$cart->product) {
            foreach ($cart->product as $cartProduct) {
                $productsById[$cartProduct->id] = null;
            }
        }

        $productListQuery = null;
        if ((bool)$cart->product) {
            $productListQuery = new Query\Product\GetListByIdList(array_keys($productsById), $regionId);
            $curl->prepare($productListQuery);
        }

        $cartItemQuery = null;
        if ((bool)$cart->product) {
            $cartItemQuery = new Query\Cart\GetItem($cart, $regionId);
            $curl->prepare($cartItemQuery);
        }

        $curl->execute();

        $user = $userItemQuery ? (new Repository\User())->getObjectByQuery($userItemQuery) : null;

        if ($productListQuery) {
            $productsById = (new Repository\Product())->getIndexedObjectListByQueryList([$productListQuery]);
        }

        // корзина из ядра
        if ($cartItemQuery) {
            $cart = $cartRepository->getObjectByQuery($cartItemQuery);
        }

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

            $widget = (new Repository\Partial\ProductCard\CartButtonBlock())->getObject($product, $cartProduct);
            $page->widgets['.' . $widget->widgetId] = $widget;

            $widget = (new Repository\Partial\Cart\ProductButton())->getObject($product, $cartProduct);
            $page->widgets['.' . $widget->widgetId] = $widget;

            $widget = (new Repository\Partial\Cart\ProductSpinner())->getObject($product, $cartProduct->quantity, true);
            $page->widgets['.' . $widget->widgetId] = $widget;
        }

        $response = new Http\JsonResponse([
            'result' => $page,
        ]);

        // FIXME: осторожно
        /*
        if (!$request->cookies[$config->session->name]) {
            $response->headers->setCookie(new Http\Cookie(
                $config->session->name,
                $session->getId(),
                time() + $config->session->cookieLifetime,
                '/',
                $config->session->cookieDomain,
                false,
                false
            ));
        }
        */

        return $response;
    }
}