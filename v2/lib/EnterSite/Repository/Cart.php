<?php

namespace EnterSite\Repository;

use Enter\Http;
use Enter\Curl\Query;
use EnterSite\ConfigTrait;
use EnterSite\Model;

class Cart {
    use ConfigTrait;

    /**
     * @param Http\Request $request
     * @return Model\Cart\Product|null
     */
    public function getProductObjectByHttpRequest(Http\Request $request) {
        $cartProduct = null;

        $productData = [
            'id'       => null,
            'quantity' => null,
        ];
        if (!empty($request->query['product']['id'])) {
            $productData = array_merge($productData, $request->query['product']);
        } else if (!empty($request->data['product']['id'])) {
            $productData = array_merge($productData, $request->data['product']);
        }

        if ($productData['id']) {
            $cartProduct = new Model\Cart\Product();
            $cartProduct->id = (string)$productData['id'];
            $cartProduct->quantity = (int)$productData['quantity'];
        }

        return $cartProduct;
    }

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

    /**
     * @param Model\Cart $cart
     * @param Model\Cart\Product $cartProduct
     */
    public function setProductForObject(Model\Cart $cart, Model\Cart\Product $cartProduct) {
        if ($cartProduct->quantity <= 0) {
            if (isset($cart->product[$cartProduct->id])) unset($cart->product[$cartProduct->id]);
        } else {
            $cart->product[$cartProduct->id] = $cartProduct;
        }
    }

    /**
     * @param $id
     * @param Model\Cart $cart
     * @return Model\Cart\Product|null
     */
    public function getProductById($id, Model\Cart $cart) {
        $return = null;

        foreach ($cart->product as $cartProduct) {
            if ($cartProduct->id === $id) {
                $return = $cartProduct;

                break;
            }
        }

        return $return;
    }
}