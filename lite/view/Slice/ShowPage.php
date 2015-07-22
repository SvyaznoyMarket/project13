<?php

namespace view\Slice;


class ShowPage extends \View\ProductCategory\CategoryPage
{

    protected $layout = 'layout/category.leaf';

    public function blockContent() {
        return $this->render('category/content.leaf', $this->params);
    }

}