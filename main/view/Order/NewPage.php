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

    // public function slotYandexMetrika() {
        // return (\App::config()->yandexMetrika['enabled']) ? $this->render('order/_yandexMetrika') : '';
    // }

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

    public function slotRuTargetOrderOneClickJS() {
        if (!$this->isOneClick() || !\App::config()->partners['RuTarget']['enabled']) return;

        /** @var $products Product[] */
        $products = $this->getParam('productsById');
        if (!$products || empty($products) || !is_array($products)) return;

        /** @var $product Product */
        $product = reset($products);
        if (!$product instanceof Product) return;

        $user = \App::user();
        $cart = $user->getOneClickCart();
        $cart->getProductById($product->getId());

        /** @var $cartProduct CartProduct */
        $cartProduct = $cart->getProductById($product->getId()) ?: null;
        if (!$cartProduct instanceof CartProduct) return;

        $data = [
            'product' => [
                'quantity' => $cartProduct->getQuantity(),
                'id' => $cartProduct->getId(),
            ],
            'regionId' => $user->getRegionId(),
        ];

        return "<div id=\"RuTargetOrderOneClickJS\" class=\"jsanalytics\" data-value=\"" . $this->json($data) . "\"></div>";
    }

    public function slotRuTargetOrderJS() {
        if ($this->isOneClick() || !\App::config()->partners['RuTarget']['enabled']) return;

        /** @var $products Product[] */
        $products = $this->getParam('productsById');
        if (!$products || empty($products) || !is_array($products)) return;

        $user = \App::user();
        $cart = $user->getCart();

        $productList = [];
        foreach ($products as $product) {
            if (!$product instanceof Product) continue;

            /** @var $cartProduct CartProduct */
            $cartProduct = $cart->getProductById($product->getId()) ?: null;
            if (!$cartProduct instanceof CartProduct) continue;

            $productList[] = [
                'qty' => $cartProduct->getQuantity(),
                'sku' => $cartProduct->getId(),
            ];
        }

        $data = [
            'products' => $productList,
            'regionId' => $user->getRegionId(),
        ];

        return "<div id=\"RuTargetOrderJS\" class=\"jsanalytics\" data-value=\"" . $this->json($data) . "\"></div>";
    }

    public function isOneClick() {
        return (bool)$this->getParam('oneClick');
    }

    public function slotMyragonPageJS() {
        $config = \App::config()->partners['Myragon'];
        if (!$config['enabled'] || !$config['enterNumber'] || !$config['secretWord'] || !$config['subdomainNumber']) {
            return;
        }

        $data = [
            'config' => [
                'enterNumber' => $config['enterNumber'],
                'secretWord' => $config['secretWord'],
                'subdomainNumber' => $config['subdomainNumber'],
            ],
            'page' => [
                'url' => null,
                'pageType' => 5,
                'pageTitle' => $this->getTitle(),
            ],
        ];

        return '<div id="myragonPageJS" class="jsanalytics" data-value="' . $this->json($data) . '"></div>';
    }
}
