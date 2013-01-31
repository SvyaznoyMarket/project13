<?php

namespace View\Service;

class CategoryPage extends \View\DefaultLayout {
    protected $layout = 'layout-twoColumn';

    public function prepare() {
        /** @var $category \Model\Product\Service\Category\Entity */
        $category = $this->getParam('category') instanceof \Model\Product\Service\Category\Entity ? $this->getParam('category') : null;
        if (!$category) {
            return;
        }

        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = [];
            $breadcrumbs[] = array(
                'name' => 'Услуги F1',
                'url'  => $this->url('service'),
            );
            $breadcrumbs[] = array(
                'name' => $category->getName(),
                'url'  => null,
            );

            $this->setParam('breadcrumbs', $breadcrumbs);
        }

        // seo: title
        $this->setTitle('F1 - ' . $category->getName() . ' - Enter.ru');
        $this->setParam('title', $category->getName());
    }

    public function slotBodyDataAttribute() {
        return 'product_catalog';
    }

    public function slotContent() {
        return $this->render('service/page-category', $this->params);
    }

    public function slotSidebar() {
        return $this->render('service/_sidebar', array_merge($this->params, array('categories' => $this->getParam('allCategories'))));
    }
}