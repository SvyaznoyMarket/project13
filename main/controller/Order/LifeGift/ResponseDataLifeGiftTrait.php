<?php

namespace Controller\Order\LifeGift;

trait ResponseDataLifeGiftTrait {
    use \Controller\Order\ResponseDataTrait {
        \Controller\Order\ResponseDataTrait::failResponseData as parentFailResponseData;
    }

    protected function failResponseData(\Exception $exception, array &$responseData) {
        $this->cart = \App::user()->getLifeGiftCart();
        $this->parentFailResponseData($exception, $responseData);
    }

    /**
     * @param $message
     * @param $responseData
     */
    protected function errorProductNotFoundHandler(&$message, &$responseData) {
        /** @var \Session\Cart\LifeGift $cart */
        $cart = $this->getCart();
        $router = \App::router();

        if (!(bool)$this->productDataById) {
            if (!(bool)$cart->getProducts()) { // если корзина пустая, то редирект на страницу корзины
                $responseData['redirect'] = $router->generate('cart');
                $message = 'Пустая корзина';
            } else {
                // Например, для ответа ядра с кодом code: 705 (Одного или нескольких товаров нет в наличии)
                $responseData['redirect'] = $router->generate('cart');
            }
        }
    }
}