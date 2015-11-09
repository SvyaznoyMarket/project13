<?php

namespace View\ProductCategory\Grid;

use \Model\Product\Category\Entity as Category;

class ChildCategoryPage extends \View\DefaultLayout {
    protected $layout = 'layout-oneColumn';

    /**
     * @var Category
     */
    protected $category;

    public function slotBodyDataAttribute() {
        return 'product_catalog';
    }

    public function slotContent() {
        return $this->render('product-category/grid/child-category', $this->params);
    }

    public function slotUserbarContent() {
        return $this->render('slice/_userbarContent', [
            'category'  => $this->getParam('category') instanceof \Model\Product\Category\Entity ? $this->getParam('category') : null,
        ]);
    }

    public function prepare() {
        $category = $this->category = $this->getParam('category', new Category());
        $this->useMenuHamburger = $this->isTchiboCategory($this->category);

        $this->flPrecheckoutData['fl-action'] = 'track-category-view';
        $this->flPrecheckoutData['fl-category-id'] = $this->category->id;

        $title = $category->getSeoTitle();
        if (!empty($title)) {
            $this->setTitle($title);
        }

        $description = $category->getSeoDescription();
        if (!empty($description)) {
            $this->addMeta('description', $description);
        }

        $keywords = $category->getSeoKeywords();
        if (!empty($keywords)) {
            $this->addMeta('keywords', $keywords);
        }
    }

    public function slotConfig() {
        return $this->tryRender('_config', ['config' => [
            'location' => ['listing'],
        ]]);
    }
}