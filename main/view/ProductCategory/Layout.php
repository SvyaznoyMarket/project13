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

        /** @var $brand \Model\Brand\Entity */
        $brand = $this->getParam('brand') instanceof \Model\Brand\Entity ? $this->getParam('brand') : null;

        // content title
        if (!$this->getParam('title')) {
            $this->setParam('title', $category->getName() . ($brand ? (' ' . $brand->getName()) : ''));
        }

        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = [];
            foreach ($category->getAncestor() as $ancestor) {
                $link = $ancestor->getLink();
                if (\App::request()->get('shop')) $link .= (false === strpos($link, '?') ? '?' : '&') . 'shop='. \App::request()->get('shop');
                $breadcrumbs[] = array(
                    'name' => $ancestor->getName(),
                    'url'  => $link,
                );
            }
            $link = $category->getLink();
            if (\App::request()->get('shop')) $link .= (false === strpos($link, '?') ? '?' : '&') . 'shop='. \App::request()->get('shop');
            $breadcrumbs[] = array(
                'name' => $category->getName(),
                'url'  => $link,
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

    public function slotInnerJavascript() {
        $category = $this->getParam('category');


        return ''
            . $this->render('_remarketingGoogle', ['tag_params' => ['pagetype' => 'category', 'pcat' => $category->getToken(), ]])
            . "\n\n"
            . $this->render('_innerJavascript');
    }

    /**
     * @param \Model\Page\Entity $page
     */
    private function applySeoPattern(\Model\Page\Entity $page) {
        $dataStore = \App::dataStoreClient();

        /** @var $category \Model\Product\Category\Entity */
        $category = $this->getParam('category') instanceof \Model\Product\Category\Entity ? $this->getParam('category') : null;
        if (!$category) {
            return;
        }

        /** @var $brand \Model\Brand\Entity */
        $brand = $this->getParam('brand') instanceof \Model\Brand\Entity ? $this->getParam('brand') : null;

        $region = \App::user()->getRegion();

        $seoTemplate = null;

        // токены категорий
        $categoryTokens = [];
        foreach ($category->getAncestor() as $iCategory) {
            $categoryTokens[] = $iCategory->getToken();
        }
        $categoryTokens[] = $category->getToken();

        if ($brand) {
            $dataStore->addQuery(sprintf('seo/brand/%s/%s.json', implode('/', $categoryTokens), $category->getToken() . '-' . $brand->getToken()), [], function ($data) use (&$seoTemplate) {
                $seoTemplate = array_merge([
                    'title'       => null,
                    'description' => null,
                    'keywords'    => null,
                ], $data);
            });
        } else {
            $dataStore->addQuery(sprintf('seo/catalog/%s.json', implode('/', $categoryTokens)), [], function ($data) use (&$seoTemplate) {
                $seoTemplate = array_merge([
                    'title'       => null,
                    'description' => null,
                    'keywords'    => null,
                ], $data);
            });
        }

        // данные для шаблона
        $patterns = [
            'категория' => [$category->getName()],
            'город'     => [$region->getName()],
            'сайт'      => null,
        ];
        if ($brand) {
            $patterns['бренд'] = [$brand->getName()];
        }

        $dataStore->addQuery(sprintf('inflect/product-category/%s.json', $category->getId()), [], function($data) use (&$patterns) {
            if ($data) $patterns['категория'] = $data;
        });
        $dataStore->addQuery(sprintf('inflect/region/%s.json', $region->getId()), [], function($data) use (&$patterns) {
            if ($data) $patterns['город'] = $data;
        });
        $dataStore->addQuery('inflect/сайт.json', [], function($data) use (&$patterns) {
            if ($data) $patterns['сайт'] = $data;
        });

        $dataStore->execute();

        if (!$seoTemplate) return;

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