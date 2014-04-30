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
        $curl = $this->getCurlClient();
        $session = $this->getSession();
        $cartRepository = new Repository\Cart();

        // корзина из сессии
        $cart = $cartRepository->getObjectByHttpSession($session);

        // товара для корзины
        $cartProduct = $cartRepository->getProductObjectByHttpRequest($request);

        // добавление товара в корзину
        $cartRepository->setProductForObject($cart, $cartProduct);

        // ид региона
        $regionId = (new Repository\Region())->getIdByHttpRequestCookie($request);

        $productItemQuery = new Query\Product\GetItemById($cartProduct->id, $regionId);
        $curl->prepare($productItemQuery);

        // токен пользователя
        $userToken = (new Repository\User)->getTokenByHttpSession($session);

        // запрос пользователя
        $userItemQuery = $userToken ? new Query\User\GetItemByToken($userToken) : null;
        if ($userItemQuery) {
            $curl->prepare($userItemQuery);
        }

        // запрос корзины
        $cartItemQuery = new Query\Cart\GetItem($cart, $regionId);
        $curl->prepare($cartItemQuery);

        $curl->execute(1, 2);

        // корзина из ядра
        $cart = $cartRepository->getObjectByQuery($cartItemQuery);

        // сохранение корзины в сессию
        $cartRepository->saveObjectToHttpSession($session, $cart);

        // товар
        $product = (new Repository\Product())->getObjectByQuery($productItemQuery);
        if (!$product) {
            $product = new Model\Product();
            $product->id = $cartProduct->id;

            throw new \Exception(sprintf('Товар #%s не найден', $cartProduct->id));
        }

        // пользователь
        $user = $userItemQuery ? (new Repository\User())->getObjectByQuery($userItemQuery) : null;

        $page = new Page();
        // кнопка купить
        $widget = (new Repository\Partial\Cart\ProductButton())->getObject($product, $cartProduct);
        $page->widgets['.' . $widget->widgetId] = $widget;
        // спиннер
        $widget = (new Repository\Partial\Cart\ProductSpinner())->getObject($product, $cartProduct, true);
        $page->widgets['.' . $widget->widgetId] = $widget;
        // пользователь, корзина
        $widget = (new Repository\Partial\UserBlock())->getObject($cart, $user);
        $page->widgets['.' . $widget->widgetId] = $widget;

        // response
        $response = new Http\JsonResponse([
            'result' => $page,
        ]);

        // информационная кука пользователя
        // TODO: вынести в Action\HandleResponse
        $response->headers->setCookie(new Http\Cookie($config->userToken->infoCookieName, 1, time() + $config->session->cookieLifetime, '/', null, false, false));

        // TODO: вынести на уровень JsonPage.result
        return $response;
    }
}