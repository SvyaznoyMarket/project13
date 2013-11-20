<?php

namespace View\ProductCategory;

trait CategoryDataTrait{

    protected function renderCategoryData(\View\Layout $page, \Model\Product\Category\Entity $category) {
        return $page->tryRender('product-category/_categoryData', array('page' => $page, 'category' => $category));
    }

}