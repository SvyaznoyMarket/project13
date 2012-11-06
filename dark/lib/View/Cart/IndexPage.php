<?php

namespace View\Cart;

class IndexPage extends \View\DefaultLayout {
    public function prepare() {
        $this->setTitle('Корзина - Enter.ru');
        $this->setParam('title', 'Моя корзина');
    }

    public function slotContent() {
        return $this->render('cart/page-index', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'cart';
    }
}
