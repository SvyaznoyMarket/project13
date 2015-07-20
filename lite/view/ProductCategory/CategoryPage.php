<?php

namespace view\ProductCategory;


use View\LiteLayout;

class CategoryPage extends LiteLayout
{

    /** @var  \Model\Product\Category\Entity */
    protected $category;

    public function prepare() {
        parent::prepare();
        $this->category = $this->getParam('category');
    }

    public function blockFixedUserbar()
    {
        return $this->render('category/_userbar.fixed', ['category' => $this->category]);
    }

}