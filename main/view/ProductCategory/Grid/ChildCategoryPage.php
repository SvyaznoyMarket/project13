<?php

namespace View\ProductCategory\Grid;

class ChildCategoryPage extends \View\DefaultLayout {
    protected $layout = 'layout-oneColumn';

    public function slotBodyDataAttribute() {
        return 'product_catalog';
    }

    public function slotContent() {
        return $this->render('product-category/grid/child-category', $this->params);
    }

    public function slotUserbar() {
        return $this->render('_userbar');
    }

    public function slotUserbarContent() {
        return $this->render('slice/_userbarContent', [
            'category'  => $this->getParam('category') instanceof \Model\Product\Category\Entity ? $this->getParam('category') : null,
        ]);
    }

    public function prepare() {
        $shopScriptSeo = $this->getParam('shopScriptSeo');
        if (!$shopScriptSeo) {
            return;
        }

        if (!empty($shopScriptSeo['title'])) {
            $this->setTitle($shopScriptSeo['title']);
        }

        if (!empty($shopScriptSeo['description'])) {
            $this->addMeta('description', $shopScriptSeo['description']);
        }

        if (!empty($shopScriptSeo['keywords'])) {
            $this->addMeta('keywords', $shopScriptSeo['keywords']);
        }
    }
}