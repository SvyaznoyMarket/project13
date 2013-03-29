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
        $tag_params = ['prodid' => [], 'pagetype' => 'productset', 'pname' => [], 'pcat' => [], 'pvalue' => []];
        foreach ($products as $product) {
            $categories = $product->getCategory();
            $category = array_pop($categories);

            $tag_params['prodid'][] = $product->getId();
            $tag_params['pname'][]  = $product->getName();
            $tag_params['pcat'][]   = $category->getToken();
            $tag_params['pvalue'][]  = $product->getPrice();

        }

        return ''
            . ($product ? $this->render('product/partner-counter/_odinkod', array('product' => $product)) : '')
            . "\n\n"
            . (bool)$product ? $this->render('_remarketingGoogle', ['tag_params' => $tag_params]) : ''
            . "\n\n"
            . $this->render('_innerJavascript');
    }
}
