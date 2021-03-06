<?php

namespace View\OrderV3;

class CartPage extends Layout {
    /** @var $products \Model\Product\Entity[]|null */
    protected $products;

    public function prepare() {
        $backlink = null;
        $cartProducts = \App::user()->getCart()->getProductsById();
        /** @var \Model\Cart\Product\Entity $cartProduct */
        $cartProduct = end($cartProducts);
        if (!empty($cartProduct->referer)) {
            $backlink = $cartProduct->referer;
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
        return $this->render('cart/page-cart-1509', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'cart';
    }

    public function slotGoogleRemarketingJS($tagParams = []) {
        /** @var $products \Model\Product\Entity[] */
        $products = $this->getParam('products');

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
}
