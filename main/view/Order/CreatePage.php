<?php

namespace View\Order;

class CreatePage extends Layout {
    public function prepare() {
        $this->setTitle('Оформление заказа - Enter');
    }

    public function slotContent() {
        if (\App::abTest()->getCase()->getKey() == 'emails') {
            return $this->render('order/page-create-abtest-email', $this->params);
        }
        return $this->render('order/page-create', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'order';
    }

    public function slotInnerJavascript() {
        /** @var $productsForRetargeting \Model\Product\Entity */

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
    }
}
