<?php

namespace View\Cart;

class IndexPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        $this->setTitle('Корзина - Enter.ru');
        $this->setParam('title', 'Моя корзина');
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
        try {
            $response = \App::contentClient()->query('footer_compact');
        } catch (\Exception $e) {
            \App::exception()->add($e);
            \App::logger()->error($e);

            $response = array('content' => '');
        }

        return $this->render('order/_footer', $this->params) . "\n\n" . $response['content'];
    }

    public function slotUserbar() {
        return '';
    }
}
