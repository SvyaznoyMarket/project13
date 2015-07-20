<?php

namespace view\Product;


use View\LiteLayout;

class IndexPage extends LiteLayout
{
    protected $layout = 'layout/product';
    /** @var \Model\Product\Entity */
    protected $product;

    public function prepare() {
        parent::prepare();
        $this->product = $this->getParam('product');
    }


    public function blockContent() {
        return $this->render('product/content', $this->params);
    }

    public function blockFixedUserbar() {
        return $this->render('product/_userbar.fixed', ['product' => $this->product]);
    }

}