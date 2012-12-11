<?php

namespace View\Order;

class CreatePage extends \View\DefaultLayout {
    protected $layout  = 'layout-order';

    public function prepare() {
        $this->setTitle('Оформление заказа - Enter');
    }

    public function slotContent() {
        return $this->render('order/page-create', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'order';
    }

    public function slotYandexMetrika() {
        return (\App::config()->yandexMetrika['enabled']) ? $this->render('order/_yandexMetrika') : '';
    }

    public function slotFooter() {
        $client = \App::contentClient();

        try {
            $response = $client->send('footer_compact');
        } catch (\Exception $e) {
            \App::exception()->add($e);
            \App::logger()->error($e);

            $response = array('content' => '');
        }

        return $response['content'];
    }
}
