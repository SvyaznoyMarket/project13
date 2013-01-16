<?php

namespace View\Order;

class BillPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        /** @var $order \Model\Order\Entity */
        $order = $this->getParam('order') instanceof \Model\Order\Entity ? $this->getParam('order') : null;
        if (!$order) {
            return;
        }

        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = array();
            $breadcrumbs[] = array(
                'name' => 'Личный кабинет',
                'url'  => null,
            );

            if ($order) {
                $breadcrumbs[] = array(
                    'name' => 'Счет для заказа №' . $order->getNumber(),
                    'url'  => null,
                );
            }

            $this->setParam('breadcrumbs', $breadcrumbs);
        }

        $this->setTitle('Счет для заказа - Личный кабинет - Enter');
        $this->setParam('title', 'Счет для заказа ' . ($order ? ('№' . $order->getNumber()) : ''));
    }

    public function slotContent() {
        return $this->render('order/page-bill', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'infopage';
    }
}
