<?php

namespace View\ProductCategory;

abstract class Layout extends \View\DefaultLayout {
    use LayoutTrait;

    public function prepare() {
        $category = $this->getParam('category');
        if (!($category instanceof \Model\Product\Category\Entity)) {
            return;
        }

        /** @var $productPager \Iterator\EntityPager */
        $productPager = $this->getParam('productPager') instanceof \Iterator\EntityPager ? $this->getParam('productPager') : null;
        /** @var $regionName string */
        $regionName = \App::user()->getRegion()->getName();

        /** @var $brand \Model\Brand\Entity */
        $brand = $this->getParam('brand') instanceof \Model\Brand\Entity ? $this->getParam('brand') : null;

        // content title
        if (!$this->getParam('title')) {
            $this->setParam('title', $category->getName() . ($brand ? (' ' . $brand->getName()) : ''));
        }

        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = $this->getBreadcrumbsPath();
            $this->setParam('breadcrumbs', $breadcrumbs);
        }

        // seo
        $page = new \Model\Page\Entity();

        if ($productPager && ($productPager->getPage() > 1)) {
            $categoryNames = [];
            foreach ($category->getAncestor() as $ancestor) {
                $categoryNames[] = $ancestor->getName();
            }
            $categoryNames[] = $category->getName();

            $page->setTitle(sprintf('%s - страница %d из %d - интернет-магазин Enter.ru - %s',
                implode(' - ', $categoryNames),
                $productPager->getPage(),
                $productPager->getLastPage(),
                $regionName
            ));
        } else {
            $page->setTitle($category->getSeoTitle());
            $page->setDescription($category->getSeoDescription());
            $page->setKeywords($category->getSeoKeywords());

            if (!$page->getTitle()) {
                $page->setTitle(''
                    . $category->getName()
                    . ($category->getRoot() ? (' - ' . $category->getRoot()->getName()) : '')
                    . ' - ' . $regionName
                    . ' - ENTER.ru'
                );
            }

            if (!$page->getDescription()) {
                $page->setDescription(''
                    . $category->getName()
                    . ' в ' . $regionName
                    . ' с ценами и описанием.'
                    . ' Купить в магазине Enter'
                );
            }

            if (!$page->getKeywords()) {
                $page->setKeywords($category->getName() . ' магазин продажа доставка ' . $regionName . ' enter.ru');
            }
        }

        $this->setTitle($page->getTitle());
        $this->addMeta('description', $page->getDescription());
        $this->addMeta('keywords', $page->getKeywords());
    }

    public function slotContentHead() {
        $ret = '';

        // заголовок контента страницы
        if (!$this->hasParam('title')) {
            $this->setParam('title', null);
        }
        // навигация
        // if (!$this->hasParam('breadcrumbs')) {
        //     $this->setParam('breadcrumbs', []);
        // }
        $this->setParam('breadcrumbs', []);

        $categoryData = $this->tryRender('product-category/_categoryData', array('page' => $this, 'category' => $this->getParam('category')));
        $contentHead = $this->render('_contentHead', array_merge($this->params, ['title' => null])); // TODO: осторожно, костыль

        if ($categoryData) $ret .= $categoryData;
        if ($contentHead) $ret .= $contentHead;

        return $ret;

    }

    public function slotMetaOg() {
        /** @var \Model\Product\Category\Entity $category  */
        $category = $this->getParam('category') instanceof \Model\Product\Category\Entity ? $this->getParam('category') : null;
        if (!$category) return '';

        return "<meta property=\"og:title\" content=\"" . $this->escape($category->getName()) . "\"/>\r\n" .
            "<meta property=\"og:image\" content=\"" . $this->escape($category->getImageUrl().'?'.time()) . "\"/>\r\n".
            "<meta property=\"og:site_name\" content=\"ENTER\"/>\r\n".
            "<meta property=\"og:type\" content=\"website\"/>\r\n";

    }

}