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
        /** @var \Model\Product\Category\Entity $category */
        $category = $this->getParam('category');

        $tag_params = [
            'pagetype' => 'category',
            'pcat' => $category->getToken(),
            'pcat_upper' => 0 == $category->getParentId() ? $category->getToken() : ($category->getRoot() ? $category->getRoot()->getToken() : '')
        ];

        return ''
        . $this->render('_remarketingGoogle', ['tag_params' => $tag_params])
        . "\n\n"
        . $this->render('_innerJavascript');
    }

} 