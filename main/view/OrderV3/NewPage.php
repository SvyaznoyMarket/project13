<?php

namespace View\OrderV3;

class NewPage extends Layout {
    public function prepare() {
        $this->setTitle('Оформление заказа - Enter');
        $errors = \App::session()->flash();
        if ($errors) {
            $this->setParam('error', $errors['errors']);
            $this->setParam('email', $errors['email']);
            $this->setParam('phone', $errors['phone']);
        }

    }

    public function slotGoogleRemarketingJS($tagParams = []) {
        $tagParams = [
            'pagetype'          => 'cart',
            'ecomm_cartvalue'   => \App::user()->getCart()->getSum()
        ];
        return parent::slotGoogleRemarketingJS($tagParams);
    }

    public function slotContent() {
        return \App::closureTemplating()->render('order-v3-new/page-new', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'order-v3-new';
    }

    public function slotHubrusJS()
    {
        $html = parent::slotHubrusJS();
        if (!empty($html)) {
            return $html . \View\Partners\Hubrus::addHubrusData('cart_items', \App::user()->getCart()->getProductsById());
        } else {
            return '';
        }
    }


}
