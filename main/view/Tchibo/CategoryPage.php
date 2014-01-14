<?php

namespace View\Tchibo;

class CategoryPage extends \View\DefaultLayout {
    protected $layout = 'layout-oneColumn';

    public function slotBodyDataAttribute() {
        return 'product_catalog';
    }

    public function slotContent() {
        return $this->render('tchibo/page-category', $this->params);
    }

    public function slotUserbar() {
        return $this->render('_userbar');
    }

    public function slotUserbarContent() {
        return $this->render('slice/_userbarContent', [
            'category'  => $this->getParam('category') instanceof \Model\Product\Category\Entity ? $this->getParam('category') : null,
        ]);
    }
}