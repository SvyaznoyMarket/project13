<?php


namespace View\OrderV3;


class LifeGiftPage extends Layout{

    public function prepare() {
        $this->setTitle('Оформление заказа Подари жизнь - Enter');
    }

    public function slotContent() {
        $template = 'page-lifegift';
        if ($this->hasParam('message')) $template = 'page-complete';
        if ($this->getParam('error') !== null) $template = 'page-error';
        return \App::closureTemplating()->render('order-v3/lifegift/' . $template, $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'order-v3-lifegift';
    }

} 