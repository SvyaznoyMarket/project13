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
        /** @var \Iterator\EntityPager $productPages */
        $productPager = $this->getParam('productPager');
        $productIds = [];
        if (is_object($productPager) && $productPager instanceof \Iterator) {
            foreach ($productPager as $product) {
                if (is_object($product) && $product instanceof \Model\Product\Entity) {
                    $productIds[] = $product->getId();
                }
            }
        }

        return $this->render('_mailRu', [
            'pageType' => 'category',
            'productIds' => $productIds,
            'price' => '',
        ]);
    }
}
