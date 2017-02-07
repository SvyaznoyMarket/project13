<?php

namespace View\ProductCategory;

class RootPage extends Layout {
    public function slotContent() {
        return $this->render('product-category/page-root', $this->params);
    }

    public function slotMyThings($data) {
        /** @var $category \Model\Product\Category\Entity */
        return parent::slotMyThings([
            'Action'    => '1011',
            'Category'  => ($category = $this->getParam('category')) && $category instanceof \Model\Product\Category\Entity ? $category->getName() : null
        ]);
    }


}
