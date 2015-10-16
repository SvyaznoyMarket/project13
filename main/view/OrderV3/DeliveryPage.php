<?php

namespace View\OrderV3;

use Session\AbTest\ABHelperTrait;

class DeliveryPage extends Layout {
    use ABHelperTrait;

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
        return \App::closureTemplating()->render('order-v3-new/__head', ['step' => 2, 'withCart' => self::isOrderWithCart()]);
    }

    public function slotContent() {
        return \App::closureTemplating()->render(self::isOrderWithCart() ? 'order-v3-new/page-delivery-with-user' : 'order-v3-new/page-delivery', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'order-v3-new';
    }

    public function slotHubrusJS() {
        $html = parent::slotHubrusJS();
        if (!empty($html)) {
            return $html . \View\Partners\Hubrus::addHubrusData('cart_items', \App::user()->getCart()->getProductsById());
        } else {
            return '';
        }
    }
}
