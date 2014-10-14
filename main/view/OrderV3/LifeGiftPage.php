<?php


namespace View\OrderV3;


class LifeGiftPage extends Layout{

    public function prepare() {
        $this->setTitle('Оформление заказа Подари жизнь - Enter');
    }

    public function slotContent() {
        return \App::closureTemplating()->render('order-v3/lifegift/page-lifegift', $this->params);
    }

    public function slotBodyDataAttribute() {
        //return 'order_new';
        return 'order-v3-life-gift';
    }

} 