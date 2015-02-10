<?php

namespace View\Order;

use \Model\Product\Entity as Product;
use \Model\Cart\Product\Entity as CartProduct;

class NewPage extends Layout {
    public function prepare() {
        $this->setTitle('Оформление заказа - Enter');
    }

    public function slotContent() {
        return $this->render('order/page-new', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'order_new';
    }

    public function slotInnerJavascript() {
        $return = ''
            . $this->render('_remarketingGoogle', ['tag_params' => ['pagetype' => 'cart_final']])
            . "\n\n"
            . $this->render('_innerJavascript');

        return $return;
    }


//    public function slotInnerJavascript() {
        /** @var $productsForRetargeting \Model\Product\Entity */

        /*
        $productsForRetargeting = $this->getParam('productsForRetargeting');

        $tag_params = ['prodid' => [], 'pname' => [], 'pcat' => [], 'ordervalue' => \App::user()->getCart()->getSum(), 'pagetype' => 'try2order'];
        foreach ($productsForRetargeting as $product) {
            $categories = $product->getCategory();
            $category = array_pop($categories);

            $tag_params['prodid'][] = $product->getId();
            $tag_params['pname'][] = $product->getName();
            $tag_params['pcat'][] = $category ? $category->getToken() : '';
        }

        return ''
            . $this->render('_remarketingGoogle', ['tag_params' => $tag_params])
            . "\n\n"
            . $this->render('_innerJavascript');
        */
//    }

    public function slotСpaexchangeJS () {
        if ( !\App::config()->partners['Сpaexchange']['enabled'] ) {
            return;
        }

        return '<div id="cpaexchangeJS" class="jsanalytics" data-value="' . $this->json(['id' => 25014]) . '"></div>';
    }

    public function isOneClick() {
        return (bool)$this->getParam('oneClick');
    }

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

        /** @var \Session\Cart $cart */
        $cart = $this->getParam('cart');

        return $this->render('_mailRu', [
            'pageType' => 'order',
            'productIds' => $productIds,
            'price' => is_object($cart) && $cart instanceof \Session\Cart ? $cart->getSum() : '',
        ]);
    }
}
