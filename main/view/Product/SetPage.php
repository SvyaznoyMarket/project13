<?php

namespace View\Product;

class SetPage extends \View\DefaultLayout {
    /** @var string */
    protected $layout  = 'layout-twoColumn';

    public function prepare() {
    }

    public function slotContent() {
        return $this->render('product/page-set', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'product_catalog';
    }

    public function slotSidebar() {
        return $this->render('product/page-set-sidebar', $this->params);
    }

    public function slotInnerJavascript() {
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
            $tagData['pvalue'][] = $product->getPrice();

        }
        $product = end($products);

        return ''
            . "\n\n"
            . (bool)$product ? $this->render('_remarketingGoogle', ['tag_params' => $tagData]) : ''
            . "\n\n"
            . $this->render('_innerJavascript');
    }
}
