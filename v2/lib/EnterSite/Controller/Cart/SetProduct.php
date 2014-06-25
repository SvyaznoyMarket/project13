<?php

namespace EnterSite\Controller\Cart;

use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\CurlClientTrait;
use EnterSite\LoggerTrait;
use EnterSite\SessionTrait;
use EnterSite\RouterTrait;
use EnterSite\Routing;
use EnterSite\Controller;
use EnterSite\Curl\Query;
use EnterSite\Model;
//use EnterSite\Model\JsonPage as Page;
use EnterSite\Repository;

class SetProduct {
    use ConfigTrait, RouterTrait, LoggerTrait, CurlClientTrait, SessionTrait {
        ConfigTrait::getConfig insteadof RouterTrait, LoggerTrait, CurlClientTrait, SessionTrait;
        LoggerTrait::getLogger insteadof CurlClientTrait, SessionTrait;
    }

    /**
     * @param Http\Request $request
     * @return Http\Response
     * @throws \Exception
     */
    public function execute(Http\Request $request) {
        $session = $this->getSession();
        $cartRepository = new Repository\Cart();

        try {
            $productId = (new Repository\Product())->getIdByHttpRequest($request);
            if (!$productId) {
                throw new \Exception(sprintf('Товар #%s не найден', $productId));
            }
            $quantity = (int)$request->query['quantity'];
            if ($quantity <= 0) {
                $quantity = 1;
            }

            // корзина из сессии
            $cart = $cartRepository->getObjectByHttpSession($session);

            $cartProduct = new Model\Cart\Product();
            $cartProduct->id = $productId;
            $cartProduct->quantity = $quantity;

            // добавление товара в корзину
            $cartRepository->setProductForObject($cart, $cartProduct);

            // сохранение корзины в сессию
            $cartRepository->saveObjectToHttpSession($session, $cart);
        } catch (\Exception $e) {
            $this->getLogger()->push(['type' => 'error', 'error' => $e, 'action' => __METHOD__, 'tag' => ['cart']]);
        }

        return (new Controller\Redirect())->execute($request->server['HTTP_REFERER'] ?: $this->getRouter()->getUrlByRoute(new Routing\Index()), 302);
    }
}