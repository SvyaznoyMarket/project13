<?php

namespace View\ProductCategory;

class Layout extends \View\DefaultLayout {
    public function prepare() {
        /** @var $category \Model\Product\Category\Entity */
        $category = $this->getParam('category') instanceof \Model\Product\Category\Entity ? $this->getParam('category') : null;
        if (!$category) {
            return;
        }
        /** @var $productPager \Iterator\EntityPager */
        $productPager = $this->getParam('productPager') instanceof \Iterator\EntityPager ? $this->getParam('productPager') : null;
        /** @var $regionName string */
        $regionName = \App::user()->getRegion()->getName();

        // content title
        if (!$this->getParam('title')) {
            $this->setParam('title', $category->getName());
        }

        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
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

        // seo: page meta
        if ($productPager && ($productPager->getPage() > 1)) {
            $categoryNames = array();
            foreach ($category->getAncestor() as $ancestor) {
                $categoryNames[] = $ancestor->getName();
            }
            $categoryNames[] = $category->getName();

            $this->setTitle(sprintf('%s - страница %d из %d - интернет-магазин Enter.ru - %s',
                implode(' - ', $categoryNames),
                $productPager->getPage(),
                $productPager->getLastPage(),
                $regionName
            ));
        } else {
            // title
            if (!$category->getSeoTitle()) {
                $category->setSeoTitle(''
                    . $category->getName()
                    . ($category->getRoot() ? (' - ' . $category->getRoot()->getName()) : '')
                    . ' - ' . $regionName
                    . ' - ENTER.ru'
                );
            }
            // description
            if (!$category->getSeoDescription()) {
                $category->setSeoDescription(''
                    . $category->getName()
                    . ' в ' . $regionName
                    . ' с ценами и описанием.'
                    . ' Купить в магазине Enter'
                );
            }
            // keywords
            if (!$category->getSeoKeywords()) {
                $category->setSeoKeywords($category->getName() . ' магазин продажа доставка ' . $regionName . ' enter.ru');
            }

            $this->setTitle($category->getSeoTitle());
            $this->addMeta('description', $category->getSeoDescription());
            $this->addMeta('keywords', $category->getSeoKeywords());
        }
    }

    public function slotBodyDataAttribute() {
        return 'product_catalog';
    }


    public function slotSidebar() {
        return $this->render('product-category/_sidebar', $this->params);
    }
}