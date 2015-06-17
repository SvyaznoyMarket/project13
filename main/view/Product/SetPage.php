<?php

namespace View\Product;

class SetPage extends \View\DefaultLayout {
    /** @var string */
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
    }

    public function slotContent() {
        return $this->render('product/page-set-new', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'product_catalog';
    }

    public function slotGoogleRemarketingJS($tagParams = []) {
        /** @var $product \Model\Product\Entity */
        $products = is_array($this->getParam('products')) ? $this->getParam('products') : [];
        $tagData = ['prodid' => [], 'pagetype' => 'productset', 'pname' => [], 'pcat' => [], 'pvalue' => []];
        foreach ($products as $product) {
            $categories = $product->getCategory();
            $category = end($categories);
            if (!$category) continue;

            $tagData['prodid'][] = $product->getId();
            $tagData['pname'][] = $product->getName();
            $tagData['pcat'][] = $category->getToken();
            $tagData['pcat_upper'][] = $product->getRootCategory() ? $product->getRootCategory()->getToken() : '';
            $tagData['pvalue'][] = $product->getPrice();

        }
        $product = end($products);
        return parent::slotGoogleRemarketingJS($tagData);
    }


    public function slotUserbarContent() {
        return $this->render('slice/_userbarContent', [
            'category'  => $this->getParam('category') instanceof \Model\Product\Category\Entity ? $this->getParam('category') : null,
        ]);
    }

    public function slotUserbarContentData() {
        return [
            'target' => '#productCatalog-filter-form',
        ];
    }

}
