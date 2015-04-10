<?php

namespace View\Cart;

class WikimartPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';
    /** @var $products \Model\Product\Entity[]|null */
    protected $products;

    public function prepare() {
        // TODO - Костыль для IE10. SITE-1919
        if(preg_match('/msie 10/i', $_SERVER['HTTP_USER_AGENT'])) {
            $this->addStylesheet('/css/basket/ie10.min.css');
        }

        $this->setTitle('Корзина - Enter.ru');
        //$this->setParam('title', 'Корзина');
    }

    public function slotContent() {
        return $this->render('cart/page-wikimart', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'cart';
    }
}
