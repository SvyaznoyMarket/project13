<?php

namespace View\ProductCategory;

trait LayoutTrait {

    public function slotBodyDataAttribute() {
        $category = $this->getParam('category') instanceof \Model\Product\Category\Entity ? $this->getParam('category') : null;
        if ($category) {
            /** @var $category \Model\Product\Category\Entity */
            if ($category->isRoot()) {
                return 'product_catalog root';
            }
        }
        return 'product_catalog';
    }


    public function slotInnerJavascript() {
        $category = $this->getParam('category');

        return ''
        . $this->render('_remarketingGoogle', ['tag_params' => ['pagetype' => 'category', 'pcat' => $category->getToken(), ]])
        . "\n\n"
        . $this->render('_innerJavascript');
    }

} 