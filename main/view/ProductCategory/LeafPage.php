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
        $category = $this->getParam('category');
        $productFilter = $this->getParam('productFilter');
        return [
            'target' => $category instanceof \Model\Product\Category\Entity && ($category->isV2() || $category->isV3()) ? '.js-listing' : '#productCatalog-filter-form',
            'filterTarget' => '#productCatalog-filter-form',
        ];
    }

    public function slotConfig() {
        return $this->tryRender('_config', ['config' => [
            'location' => ['listing'],
        ]]);
    }
}
