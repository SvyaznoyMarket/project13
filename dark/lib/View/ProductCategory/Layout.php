<?php

namespace View\ProductCategory;

class Layout extends \View\DefaultLayout {
    public function prepare() {
        /** @var $category \Model\Product\Category\Entity */
        $category = $this->getParam('category') instanceof \Model\Product\Category\Entity ? $this->getParam('category') : null;

        if (!$this->getParam('title')) {
            $this->setParam('title', $category ? $category->getName() : '');
        }

        if (!$this->hasParam('breadcrumbs') && $category) {
            $breadcrumbs = array();
            foreach ($category->getAncestor() as $ancestor) {
                $breadcrumbs[] = array(
                    'name' => $ancestor->getName(),
                    'url'  => $ancestor->getLink(),
                );
            }
            $breadcrumbs[] = array(
                'name' => $category->getName(),
                'url'  => $category->getLink(),
            );

            $this->setParam('breadcrumbs', $breadcrumbs);
        }
    }

    public function slotBodyDataAttribute() {
        return 'product_catalog';
    }


    public function slotSidebar() {
        return $this->render('product-category/_sidebar', $this->params);
    }
}