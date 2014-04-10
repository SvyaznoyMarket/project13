<?php

namespace EnterSite\Repository;

use Enter\Http;
use Enter\Curl\Query;
use EnterSite\ConfigTrait;
use EnterSite\Model;

class Cart {
    use ConfigTrait;

    /**
     * @param Http\Session $session
     * @return Model\Cart
     */
    public function getObjectByHttpSession(Http\Session $session) {
        $cart = new Model\Cart();

        $cartData = array_merge([
            'productList' => [],
        ], (array)$session->get('userCart'));

        foreach ($cartData['productList'] as $productId => $productQuantity) {
            $cartProduct = new Model\Cart\Product();
            $cartProduct->id = (string)$productId;
            $cartProduct->quantity = (int)$productQuantity;

            $cart->product[$cartProduct->id] = $cartProduct;
        }

        return $cart;
    }

    /**
     * @param Http\Session $session
     * @param Model\Cart $cart
     */
    public function saveObjectToHttpSession(Http\Session $session, Model\Cart $cart) {
        // TODO: купоны, ...

        $cartData = [
            'productList' => [],
        ];

        foreach ($cart->product as $cartProduct) {
            $cartData['productList'][$cartProduct->id] = $cartProduct->quantity;
        }

        $session->set('userCart', $cartData);
    }

    /**
     * @param Query $query
     * @return Model\Cart
     */
    public function getObjectByQuery(Query $query) {
        $cart = null;

        $item = $query->getResult();
        $cart = new Model\Cart($item);

        return $cart;
    }
}