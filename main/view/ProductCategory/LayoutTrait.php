<?php

namespace View\ProductCategory;

trait LayoutTrait {

    public function slotBodyDataAttribute() {
        $category = $this->getParam('category');
        if ($category instanceof \Model\Product\Category\Entity) {
            if ($category->isRoot()) {
                return 'product_catalog root';
            }
        }
        return 'product_catalog';
    }


    public function slotInnerJavascript() {
        $category = $this->getParam('category');
        if (!($category instanceof \Model\Product\Category\Entity)) {
            return;
        }

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