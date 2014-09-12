<?php

namespace View\ProductCategory;

class LeafPage extends Layout {
    protected $layout  = 'layout-oneColumn';

    public function slotContent() {
        $this->params['request'] = \App::request();

        return $this->render('product-category/page-leaf', $this->params);
    }

    public function slotUserbar() {
        return $this->render('_userbar');
    }

    public function slotUserbarContent() {
        return $this->render('product-category/_userbarContent', [
            'category'  => $this->getParam('category') instanceof \Model\Product\Category\Entity ? $this->getParam('category') : null,
        ]);
    }

    public function slotUserbarContentData() {
        return [
            'target' => '#productCatalog-filter-form',
        ];
    }

    public function slotMailRu() {
        return $this->render('_mailRu', [
            'pageType' => 'category',
            'productIds' => [],
            'price' => '',
        ]);
    }
}
