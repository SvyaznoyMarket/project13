<?php

namespace View\Tchibo;

class IndexPage extends \View\DefaultLayout {
    protected $layout = 'layout-oneColumn';
    protected $useTchiboAnalytics = true;

    public function prepare() {
        $category = $this->getParam('category');
        if (!($category instanceof \Model\Product\Category\Entity)) {
            return;
        }

        $this->setParam('title', $category->getSeoTitle());
        $this->setTitle($category->getSeoTitle());
        $this->addMeta('description', $category->getSeoDescription());
        $this->addMeta('keywords', $category->getSeoKeywords());
    }

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