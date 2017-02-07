<?php

namespace View\OrderV3;

use Session\AbTest\ABHelperTrait;

class DeliveryPage extends Layout {
    use ABHelperTrait;

    public $isStepDelivery = true;

    public function prepare() {
        $this->setTitle('Оформление заказа - Enter');
    }

    public function slotGoogleRemarketingJS($tagParams = []) {
        $tagParams = [
            'pagetype'          => 'cart',
            'ecomm_cartvalue'   => \App::user()->getCart()->getSum()
        ];
        return parent::slotGoogleRemarketingJS($tagParams);
    }

    public function slotOrderHead() {
        return \App::closureTemplating()->render('order-v3-new/__head', ['step' => 2]);
    }

    public function slotContent() {
        return \App::closureTemplating()->render('order-v3-new/page-delivery', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'order-v3-new';
    }
}
