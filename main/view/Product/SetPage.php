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
        $product = $this->getParam('product') instanceof \Model\Product\Entity ? $this->getParam('product') : null;
        $categories = $product->getCategory();
        $category = array_pop($categories);

        return ''
            . ($product ? $this->render('product/_odinkod', array('product' => $product)) : '')
            . "\n\n"
            . (bool)$product ? $this->render('_remarketingGoogle', ['tag_params' => ['prodid' => $product->getId(), 'pagetype' => 'product', 'pname' => $product->getName(), 'pcat' => ($category) ? $category->getToken() : '', 'value' => $product->getPrice()]]) : ''
            . "\n\n"
            . $this->render('_innerJavascript');
    }
}
