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
     * @throws \Exception
     * @return Http\JsonResponse
     */
    public function execute(Http\Request $request) {
        $config = $this->getConfig();
        $curl = $this->getCurlClient();
        $cartRepository = new Repository\Cart();

        $productData = array_merge([
            'id'       => null,
            'quantity' => null,
        ], (array)$request->data['product']);

        // ид региона
        $regionId = (new Repository\Region())->getIdByHttpRequest($request);

        $productItemQuery = new Query\Product\GetItemById($productData['id'], $regionId);
        $curl->prepare($productItemQuery);

        $curl->execute(1, 2);

        $product = (new Repository\Product())->getObjectByQuery($productItemQuery);
        if (!$product) {
            $product = new Model\Product();
            $product->id = $productData['id'];

            throw new \Exception(sprintf('Товар #%s не найден', $productData['id']));
        }

        $cartProduct = new Model\Cart\Product();
        $cartProduct->id = $productData['id'];
        $cartProduct->quantity = (int)$productData['quantity'];

        $session = $this->getSession();
        $cart = $cartRepository->getObjectByHttpSession($session);
        $cart->product[$cartProduct->id] = $cartProduct;
        $cartRepository->saveObjectToHttpSession($session, $cart);

        $page = new Page();
        // кнопка купить
        $widget = (new Repository\Partial\Cart\ProductButton())->getObject($product, $cartProduct);
        $page->widgets['.' . $widget->widgetId] = $widget;
        // спиннер
        $widget = (new Repository\Partial\Cart\ProductSpinner())->getObject($product, $cartProduct);
        $page->widgets['.' . $widget->widgetId] = $widget;

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