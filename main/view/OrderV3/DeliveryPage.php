<?php

namespace View\OrderV3;

class DeliveryPage extends Layout {
    public function prepare() {
        $this->setTitle('Оформление заказа - Enter');
    }

    public function slotGoogleRemarketingJS() {
        $tagParams = [
            'pagetype'          => 'cart',
            'ecomm_cartvalue'   => \App::user()->getCart()->getSum()
        ];
        return parent::slotGoogleRemarketingJS($tagParams);
    }

    public function slotContent() {
        $path = 'order-v3';

        $region = \App::user()->getRegion();
        if ($region && \App::config()->newOrder) {
            $ordersNewTest = \App::abTest()->getTest('orders_new');
            $ordersNewSomeRegionsTest = \App::abTest()->getTest('orders_new_some_regions');
            if (
                (!in_array($region->getId(), [93746, 119623]) && $ordersNewTest && in_array($ordersNewTest->getChosenCase()->getKey(), ['new_2'], true)) // АБ-тест для остальных регионов
                || (in_array($region->getId(), [93746, 119623]) && $ordersNewSomeRegionsTest && in_array($ordersNewSomeRegionsTest->getChosenCase()->getKey(), ['new_2'], true)) // АБ-тест для Ярославля и Ростова-на-дону
            ) {
                $path = 'order-v3-new';
            }
        }

        return \App::closureTemplating()->render( $path . '/page-delivery', $this->params);
    }

    public function slotBodyDataAttribute() {
        $region = \App::user()->getRegion();
        if ($region && \App::config()->newOrder) {
            $ordersNewTest = \App::abTest()->getTest('orders_new');
            $ordersNewSomeRegionsTest = \App::abTest()->getTest('orders_new_some_regions');
            if (
                (!in_array($region->getId(), [93746, 119623]) && $ordersNewTest && in_array($ordersNewTest->getChosenCase()->getKey(), ['new_2'], true)) // АБ-тест для остальных регионов
                || (in_array($region->getId(), [93746, 119623]) && $ordersNewSomeRegionsTest && in_array($ordersNewSomeRegionsTest->getChosenCase()->getKey(), ['new_2'], true)) // АБ-тест для Ярославля и Ростова-на-дону
            ) {
                return 'order-v3-new';
            }
        }

        return 'order-v3';
    }

    public function slotHubrusJS() {
        $html = parent::slotHubrusJS();
        if (!empty($html)) {
            $products = \App::user()->getCart()->getProductsNC();
            return $html . \View\Partners\Hubrus::addHubrusData('cart_items', $products);
        } else {
            return '';
        }
    }
}
