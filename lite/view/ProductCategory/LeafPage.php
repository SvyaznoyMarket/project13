<?php

namespace View\ProductCategory;


use View\LiteLayout;

class LeafPage extends LiteLayout
{

    protected $layout = 'layout/category.leaf';

    public function blockContent() {
        return $this->render('category/content.leaf', $this->params);
    }

}