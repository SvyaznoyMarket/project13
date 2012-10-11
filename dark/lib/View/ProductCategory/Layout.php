<?php

namespace View\ProductCategory;

class Layout extends \View\DefaultLayout {
    public function prepare() {
        if (!$this->getParam('title')) {
            $this->setParam('title', $this->getParam('category') instanceof \Model\Product\Category\Entity ? $this->getParam('category')->getName() : '');
        }

        if (!$this->hasParam('breadcrumbs') && $this->getParam('category') instanceof \Model\Product\Category\Entity) {
            /** @var $category \Model\Product\Category\Entity */
            $category = $this->getParam('category');
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
}