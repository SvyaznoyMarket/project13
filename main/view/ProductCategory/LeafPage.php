<?php

namespace View\ProductCategory;

class LeafPage extends Layout {
    protected $layout  = 'layout-oneColumn';

    public function slotContent() {
        $this->params['request'] = \App::request();

        return $this->render('product-category/page-leaf', $this->params);
    }

    public function slotUserbarContent() {
        $category = $this->getParam('category');
        $productFilter = $this->getParam('productFilter');

        return $this->render('product-category/_userbarContent', [
            'category'  => $category instanceof \Model\Product\Category\Entity ? $category : null,
            'productFilter'  => $productFilter instanceof \Model\Product\Filter ? $productFilter : null,
        ]);
    }

    public function slotUserbarContentData() {
        $productFilter = $this->getParam('productFilter');
        return [
            'target' => $productFilter instanceof \Model\Product\Filter && $productFilter->hasAlwaysShowFilters() ? '.js-listing' : '#productCatalog-filter-form',
            'filterTarget' => '#productCatalog-filter-form',
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
