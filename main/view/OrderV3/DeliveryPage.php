<?php

namespace View\OrderV3;

class DeliveryPage extends Layout {
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

    public function slotContent() {
        return \App::closureTemplating()->render( 'order-v3-new/page-delivery', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'order-v3-new';
    }

    public function slotHubrusJS() {
        $html = parent::slotHubrusJS();
        if (!empty($html)) {
            $products = \App::user()->getCart()->getProductData();
            return $html . \View\Partners\Hubrus::addHubrusData('cart_items', $products);
        } else {
            return '';
        }
    }
}
