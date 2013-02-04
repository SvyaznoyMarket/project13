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
            $breadcrumbs = [];
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

            // title
            if (!$page->getTitle()) {
                $page->setTitle(''
                    . $category->getName()
                    . ($category->getRoot() ? (' - ' . $category->getRoot()->getName()) : '')
                    . ' - ' . $regionName
                    . ' - ENTER.ru'
                );
            }
            // description
            if (!$page->getDescription()) {
                $page->setDescription(''
                    . $category->getName()
                    . ' в ' . $regionName
                    . ' с ценами и описанием.'
                    . ' Купить в магазине Enter'
                );
            }
            // keywords
            if (!$page->getKeywords()) {
                $page->setKeywords($category->getName() . ' магазин продажа доставка ' . $regionName . ' enter.ru');
            }

            try {
                $this->applySeoPattern($page);
            } catch (\Exception $e) {
                \App::logger()->error($e);
            }
        }

        $this->setTitle($page->getTitle());
        $this->addMeta('description', $page->getDescription());
        $this->addMeta('keywords', $page->getKeywords());
    }

    public function slotBodyDataAttribute() {
        return 'product_catalog';
    }


    public function slotSidebar() {
        return $this->render('product-category/_sidebar', $this->params);
    }

    private function applySeoPattern(\Model\Page\Entity $page) {
        $dataStore = \App::dataStoreClient();

        /** @var $category \Model\Product\Category\Entity */
        $category = $this->getParam('category') instanceof \Model\Product\Category\Entity ? $this->getParam('category') : null;
        if (!$category) {
            return;
        }

        $seoTemplate = null;
        foreach (array_reverse(array_merge($category->getAncestor(), [$category])) as $iCategory) {
            /** @var $iCategory \Model\Product\Category\Entity */
            $seoTemplate = $dataStore->query(sprintf('seo/%s.json', trim($iCategory->getLink(), '/')));
            if ((bool)$seoTemplate) break;
        }
        if (!$seoTemplate) return;

        $patterns = [
            'категория' => $dataStore->query(sprintf('inflect/product-category/%s.json', $category->getId())),
            'город'     => $dataStore->query(sprintf('inflect/region/%s.json', \App::user()->getRegion()->getId())),
            'сайт'      => $dataStore->query('inflect/сайт.json'),
        ];

        $replacer = new \Util\InflectReplacer($patterns);
        if ($value = $replacer->get($seoTemplate['title'])) {
            $page->setTitle($value);
        }
        if ($value = $replacer->get($seoTemplate['description'])) {
            $page->setDescription($value);
        }
        if ($value = $replacer->get($seoTemplate['keywords'])) {
            $page->setKeywords($value);
        }
    }
}