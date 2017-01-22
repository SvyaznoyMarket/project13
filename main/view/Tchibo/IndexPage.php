<?php

namespace View\Tchibo;

class IndexPage extends \View\ProductCategory\Layout {
    protected $layout = 'layout-oneColumn';
    protected $useTchiboAnalytics = true;
    protected $useMenuHamburger = true;

    public function slotBodyDataAttribute() {
        return 'product_catalog';
    }

    public function slotContent() {
        return $this->render('tchibo/page-index', $this->params);
    }

    public function slotUserbarContent() {
        return $this->render('jewel/product-category/_userbarContent', [
            'category'  => $this->getGlobalParam('rootCategoryInMenu'),
        ]);
    }
}