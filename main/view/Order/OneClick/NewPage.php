<?php

namespace View\Order\OneClick;

class NewPage extends \View\Order\NewPage {
    public function slotMailRu() {
        $products = $this->getParam('productsById');
        $productIds = [];
        if (is_array($products)) {
            foreach ($products as $product) {
                if (is_object($product) && $product instanceof \Model\Product\Entity) {
                    $productIds[] = $product->getId();
                }
            }
        }

        /** @var \Session\Cart\OneClick $cart */
        $cart = $this->getParam('cart');

        return $this->render('_mailRu', [
            'pageType' => 'one_click_order',
            'productIds' => $productIds,
            'price' => is_object($cart) && $cart instanceof \Session\Cart\OneClick ? $cart->getSum() : '',
        ]);
    }
}
