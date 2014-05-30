<?php

namespace View\Cart;

class IndexPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        $this->setTitle('Корзина - Enter.ru');
        $this->setParam('title', 'Корзина');
    }

    public function slotContent() {
        return
            (bool)\App::user()->getCart()->count()
            ? $this->render('cart/page-index', $this->params)
            : $this->render('cart/page-empty', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'cart';
    }

    public function slotFooter() {
        $client = \App::contentClient();

        $response = null;
        $client->addQuery(
            'footer_compact',
            [],
            function($data) use (&$response) {
                $response = $data;
            },
            function(\Exception $e) {
                \App::exception()->add($e);
            }
        );
        $client->execute();

        $response = array_merge(['content' => ''], (array)$response);

        return $this->render('order/_footer', $this->params) . "\n\n" . $response['content'];
    }

    public function slotInnerJavascript() {
        /** @var $products \Model\Product\Entity[] */
        $products = $this->getParam('productEntities');

        $cart = \App::user()->getCart();
        $tag_params = ['prodid' => [], 'pname' => [], 'pcat' => [], 'cartvalue' => $cart->getSum(), 'pagetype' => 'cart'];
        foreach ($products as $product) {
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
    }

    public function slotUserbar() {
        return '';
    }

    public function slotСpaexchangeJS () {
        if ( !\App::config()->partners['Сpaexchange']['enabled'] ) {
           return;
        }

        return '<div id="cpaexchangeJS" class="jsanalytics" data-value="' . $this->json(['id' => 25013]) . '"></div>';
    }

    public function slotRuTargetCartJS() {
        if (!\App::config()->partners['RuTarget']['enabled']) return;

        $productsInfo = [];
        $cart = \App::user()->getCart();
        foreach ($cart->getProducts() as $product) {
            if (!$product instanceof \Model\Cart\Product\Entity) continue;

            $productsInfo[] = [
                'sky' => $product->getId(),//product id
                'qty' => $product->getQuantity(),//product quantity
            ];
        }

        $data = [
            'products' => $productsInfo,
            'regionId' => \App::user()->getRegionId(),
        ];

        return "<div id=\"RuTargetCartJS\" class=\"jsanalytics\" data-value=\"" . $this->json($data) . "\"><div>";
    }

    public function slotMyragonPageJS() {
        $config = \App::config()->partners['Myragon'];
        if (!$config['enabled'] || !$config['enterNumber'] || !$config['secretWord'] || !$config['subdomainNumber']) {
            return;
        }

        $cart = \App::user()->getCart();
        if (!$cart) {
            return;
        }

        $basketProducts = [];
        foreach ($cart->getProducts() as $product) {
            if (!$product instanceof \Model\Cart\Product\Entity) continue;

            $basketProducts[] = [
                'id' => $product->getId(),
                'price' => $product->getPrice(),
                'currency' => 'RUB',
                'amount' => $product->getQuantity(),
            ];
        }

        $data = [
            'config' => [
                'enterNumber' => $config['enterNumber'],
                'secretWord' => $config['secretWord'],
                'subdomainNumber' => $config['subdomainNumber'],
            ],
            'page' => [
                'url' => null,
                'pageType' => 4,
                'pageTitle' => $this->getTitle(),
                'basketProducts' => $basketProducts,
            ],
        ];

        return '<div id="myragonPageJS" class="jsanalytics" data-value="' . $this->json($data) . '"></div>';
    }
}
