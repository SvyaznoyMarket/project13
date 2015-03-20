<?php

namespace View\Order;

use \Model\Product\Entity as Product;
use \Model\Cart\Product\Entity as CartProduct;

/**
 * Class NewPage
 * @package View\Order
 * @deprecated
 */
class NewPage extends Layout {
    public function prepare() {
        $this->setTitle('Оформление заказа - Enter');
    }

    public function slotContent() {
        return $this->render('order/page-new', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'order_new';
    }

    public function slotСpaexchangeJS () {
        if ( !\App::config()->partners['Сpaexchange']['enabled'] ) {
            return;
        }

        return '<div id="cpaexchangeJS" class="jsanalytics" data-value="' . $this->json(['id' => 25014]) . '"></div>';
    }

    public function isOneClick() {
        return (bool)$this->getParam('oneClick');
    }

}
