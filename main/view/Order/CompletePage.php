<?php

namespace View\Order;

class CompletePage extends Layout {
    public function prepare() {
        $orders = is_array($this->getParam('orders')) ? $this->getParam('orders') : [];
        /** @var $order \Model\Order\Entity */
        $order = reset($orders);
        if ($order) {
            // если банк Ренессанс
            if (($order->getCredit() instanceof \Model\Order\Credit\Entity) && (\Model\CreditBank\Entity::PROVIDER_DIRECT_CREDIT == $order->getCredit()->getBankProviderId())) {
                $this->addStylesheet('http://direct-credit.ru/widget/style.css');
            }
        }

        $this->setTitle('Оформление заказа - Enter');
    }

    public function slotContent() {
        return $this->render('order/page-complete', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'order_complete';
    }

    public function slotYandexMetrika() {
        return (\App::config()->yandexMetrika['enabled']) ? $this->render('order/_yandexMetrika') : '';
    }

    public function slotInnerJavascript() {
        /** @var $orders \Model\Order\Entity[] */
        $orders = $this->getParam('orders');
        $productsById = $this->getParam('productsById');

        $isOrderAnalytics = $this->getParam('isOrderAnalytics');

        $isOrderAnalytics = (null === $isOrderAnalytics) ? $isOrderAnalytics : true;

        $tag_params = ['prodid' => [], 'pname' => [], 'pcat' => [], 'value' => [], 'pagetype' => 'purchase'];
        foreach ($orders as $order) {
            foreach ($order->getProduct() as $orderProduct) {
                if (!isset($productsById[$orderProduct->getId()])) continue;
                /** @var $product \Model\Product\Entity */
                $product = $productsById[$orderProduct->getId()];
                $categories = $product->getCategory();
                $category = array_pop($categories);

                $tag_params['prodid'][] = $product->getId();
                $tag_params['pname'][] = $product->getName();
                $tag_params['pcat'][] = $category ? $category->getToken() : '';
                $tag_params['value'][] = $orderProduct->getPrice();
            }
        }

        return ''
            . ($isOrderAnalytics ? $this->render('_remarketingGoogle', ['tag_params' => $tag_params]) : '')
            . "\n\n"
            . $this->render('_innerJavascript');
    }
}
