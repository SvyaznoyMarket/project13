<?php

namespace View\ProductCategory\Grid;

use View\ProductCategory\Layout;

class AutoGridPage extends Layout
{

    protected $useTchiboAnalytics = true;

    public function slotBodyDataAttribute() {
        return 'product_catalog';
    }

    public function slotContent() {
        return $this->render('product-category/grid/autogrid-category', $this->params);
    }

    public function slotUserbarContent() {
        return $this->render('jewel/product-category/_userbarContent', [
            'category'  => $this->getGlobalParam('rootCategoryInMenu'),
        ]);
    }
}