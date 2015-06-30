<?php

namespace View\Cart;

class IndexPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';
    /** @var $products \Model\Product\Entity[]|null */
    protected $products;

    public function prepare() {
        // TODO - Костыль для IE10. SITE-1919
        if(preg_match('/msie 10/i', $_SERVER['HTTP_USER_AGENT'])) {
            $this->addStylesheet('/css/basket/ie10.min.css');
        }

        $backlink = null;
        $productData = \App::user()->getCart()->getProductData();
        $productData = is_array($productData) ? end($productData) : [];
        if (!empty($productData['referer'])) {
            $backlink = $productData['referer'];
        }
        if (!$backlink && $this->hasParam('products')) {
            $this->products = $this->getParam('products');
            foreach (array_reverse($this->products) as $product) {
                /** @var $product \Model\Product\Entity */
                if ($product->getRootCategory() instanceof \Model\Product\Category\Entity) {
                    $backlink = $product->getRootCategory()->getLink();
                    break;
                }
            }
        }
        if (!$backlink) {
            $backlink = '/';
        }

        $this->setTitle('Корзина - Enter.ru');
        $this->setParam('title', 'Корзина');
        $this->setParam('backlink', $backlink);
    }

    public function slotContent() {
        return $this->render('cart/page-cart', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'cart';
    }

    public function slotGoogleRemarketingJS($tagParams = []) {
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
            $tag_params['pcat_upper'][] = $product->getRootCategory() ? $product->getRootCategory()->getToken() : '';
        }

        return parent::slotGoogleRemarketingJS($tag_params);
    }


    public function slotСpaexchangeJS () {
        if ( !\App::config()->partners['Сpaexchange']['enabled'] ) {
           return '';
        }

        return '<div id="cpaexchangeJS" class="jsanalytics" data-value="' . $this->json(['id' => 25013]) . '"></div>';
    }

    public function slotHubrusJS() {
        $html = parent::slotHubrusJS();
        if (!empty($html)) {
            return $html . \View\Partners\Hubrus::addHubrusData('cart_items', $this->products);
        } else {
            return '';
        }
    }

    public function slotMyThings($data) {
        $data = ['Action' => '1013'];
        if (\App::user()->getCart()->count()) {
            /** @var $product \Model\Cart\Product\Entity */
            $products = \App::user()->getCart()->getProducts();
            $product = end($products); // Последний продукт
            $data['ProductId'] = (string)$product->getId();
        }
        return parent::slotMyThings($data);
    }


}
