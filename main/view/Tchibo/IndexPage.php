<?php

namespace View\Tchibo;

use \Model\Product\Category\Entity as Category;

class IndexPage extends \View\DefaultLayout {
    protected $layout = 'layout-oneColumn';
    protected $useTchiboAnalytics = true;
    protected $useMenuHamburger = true;

    /**
     * @var Category
     */
    protected $category;

    public function prepare() {

        $category = $this->category = $this->getParam('category', new Category());

        $this->flPrecheckoutData['fl-action'] = 'track-category-view';
        $this->flPrecheckoutData['fl-category-id'] = $this->category->id;


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