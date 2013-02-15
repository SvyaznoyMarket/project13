<?php

namespace View\Cart;

class IndexPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        $this->setTitle('Корзина - Enter.ru');
        $this->setParam('title', 'Моя корзина');
    }

    public function slotContent() {
        return
            (bool)\App::user()->getCart()->count()
            ? $this->render('cart/page-index', $this->params)
            : $this->render('cart/page-empty', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'cart';
    }

    public function slotFooter() {
        try {
            $response = \App::contentClient()->query('footer_compact');
        } catch (\Exception $e) {
            \App::exception()->add($e);
            \App::logger()->error($e);

            $response = array('content' => '');
        }

        return $this->render('order/_footer', $this->params) . "\n\n" . $response['content'];
    }

    public function slotInnerJavascript() {
        /** @var $products \Model\Product\Entity[] */
        $products = $this->getParam('products');

        $tag_params = ['prodid' => [], 'pname' => [], 'pcat' => [], 'value' => [], 'pagetype' => 'cart'];
        foreach ($products as $product) {
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

    public function slotUserbar() {
        return '';
    }
}
