<?php

namespace View\Order;

class CreatePage extends Layout {
    public function prepare() {
        $this->setTitle('Оформление заказа - Enter');
    }

    public function slotContent() {
        return $this->render('order/page-create', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'order';
    }

    public function slotYandexMetrika() {
        return (\App::config()->yandexMetrika['enabled']) ? $this->render('order/_yandexMetrika') : '';
    }

    public function slotInnerJavascript() {
        /** @var $productsForRetargeting \Model\Product\Entity */

        $productsForRetargeting = $this->getParam('productsForRetargeting');

        $tag_params = ['prodid' => [], 'pname' => [], 'pcat' => [], 'value' => [], 'pagetype' => 'cart'];
        foreach ($productsForRetargeting as $product) {
            $category = array_pop($product->getCategory());

            $tag_params['prodid'][] = $product->getId();
            $tag_params['pname'][] = $product->getName();
            $tag_params['pcat'][] = $category ? $category->getToken() : '';
            $tag_params['value'][] = $product->getPrice();
        }

        return ''
            . $this->render('_remarketingGoogle', ['tag_params' => $tag_params])
            . "\n\n"
            . $this->render('_innerJavascript');
    }
}
