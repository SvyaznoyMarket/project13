<?php

namespace View\Gift\ProductCategory;

class LeafPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function slotBodyDataAttribute() {
        return 'gift';
    }

    public function slotContent() {
        return $this->render('gift/category/page-leaf', $this->params);
    }

    public function slotUserbarContent() {
        $productFilter = $this->getParam('productFilter');

        return $this->render('product-category/_userbarContent', [
            'productFilter'  => $productFilter instanceof \Model\Product\Filter ? $productFilter : null,
            'v2' => true
        ]);
    }

    public function slotUserbarContentData() {
        return [
            'target' => '.js-listing',
            'filterTarget' => '.js-gift-category',
        ];
    }

    public function slotConfig() {
        return $this->tryRender('_config', ['config' => [
            'location' => ['listing'],
        ]]);
    }
}
