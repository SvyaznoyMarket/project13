<?php

namespace View\ProductCategory;


class LeafPage extends CategoryPage
{

    protected $layout = 'layout/category.leaf';

    public function prepare() {
        parent::prepare();
        $this->category = $this->getParam('category');
    }

    public function blockFixedUserbar()
    {
        return $this->render('category/_userbar.fixed', ['category' => $this->category]);
    }

    public function blockContent() {
        return $this->render('category/content.leaf', $this->params);
    }

}