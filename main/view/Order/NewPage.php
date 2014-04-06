<?php

namespace View\Order;

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

    // public function slotYandexMetrika() {
        // return (\App::config()->yandexMetrika['enabled']) ? $this->render('order/_yandexMetrika') : '';
    // }

//    public function slotInnerJavascript() {
        /** @var $productsForRetargeting \Model\Product\Entity */

        /*
        $productsForRetargeting = $this->getParam('productsForRetargeting');

        $tag_params = ['prodid' => [], 'pname' => [], 'pcat' => [], 'ordervalue' => \App::user()->getCart()->getSum(), 'pagetype' => 'try2order'];
        foreach ($productsForRetargeting as $product) {
            $categories = $product->getCategory();
            $category = array_pop($categories);

            $tag_params['prodid'][] = $product->getId();
            $tag_params['pname'][] = $product->getName();
            $tag_params['pcat'][] = $category ? $category->getToken() : '';
        }

        return ''
            . $this->render('_remarketingGoogle', ['tag_params' => $tag_params])
            . "\n\n"
            . $this->render('_innerJavascript');
        */
//    }

    public function slotСpaexchangeJS () {
        return '<div id="cpaexchangeJS" class="jsanalytics" data-value="' . $this->json(['id' => 25014]) . '"></div>';
    }
}
