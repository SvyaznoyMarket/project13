<?php

namespace view\Product;


use View\LiteLayout;

class IndexPage extends LiteLayout
{
    protected $layout = 'layout/product';

    public function blockContent() {
        return $this->render('product/content', $this->params);
    }

}