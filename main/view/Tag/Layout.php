<?php

namespace View\Tag;

class Layout extends \View\DefaultLayout {
    public function prepare() {
        /** @var $category \Model\Product\Category\Entity */
        $category = $this->getParam('category') instanceof \Model\Product\Category\Entity ? $this->getParam('category') : null;
        /** @var $tag \Model\Tag\Entity */
        $tag = $this->getParam('tag') instanceof \Model\Tag\Entity ? $this->getParam('tag') : null;
        if (!$tag) {
            return;
        }
        $categoryToken = $this->getParam('categoryToken');
        $category = $this->getParam('category');
        $rootCategory = $this->getParam('rootCategory');

        /** @var $productPager \Iterator\EntityPager */
        $productPager = $this->getParam('productPager') instanceof \Iterator\EntityPager ? $this->getParam('productPager') : null;
        /** @var $regionName string */
        $regionName = \App::user()->getRegion()->getName();

        // content title
        if (!$this->getParam('title')) {
            $this->setParam('title', $tag->getName() . ($category ? (': ' . $category->getName()) : ''));
        }

        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = [];
            $breadcrumbs[] = array(
                'name' => 'Тэги',
                'url'  => '',
                'span' => true,
            );
            $breadcrumbs[] = array(
                'name' => $tag->getName(),
                'url'  => $this->url('tag', ['tagToken' => $tag->getToken()]),
            );

            if(!empty($category)) {
                if(!$category->isRoot() && !empty($rootCategory)) {
                    $breadcrumbs[] = array(
                        'name' => $rootCategory->getName(),
                        'url'  => $this->url('tag.category', ['tagToken' => $tag->getToken(), 'categoryToken' => $rootCategory->getToken()]),
                    );
                }
                $breadcrumbs[] = array(
                    'name' => $category->getName(),
                    'url'  => $this->url('tag.category', ['tagToken' => $tag->getToken(), 'categoryToken' => $category->getToken()]),
                );
            }

            $this->setParam('breadcrumbs', $breadcrumbs);
        }

        // seo
        $page = new \Model\Page\Entity();

        // title
        if (!$page->getTitle()) {
            $page->setTitle(''
                . $tag->getName()
                . ' - ' . $regionName
                . ' - ENTER.ru'
            );
        }
        // description
        if (!$page->getDescription()) {
            $page->setDescription(''
                . $tag->getName()
                . ' в ' . $regionName
                . ' с ценами и описанием.'
                . ' Купить в магазине Enter'
            );
        }
        // keywords
        if (!$page->getKeywords()) {
            $page->setKeywords($tag->getName() . ' магазин продажа доставка ' . $regionName . ' enter.ru');
        }

        $this->setTitle($page->getTitle());
        $this->addMeta('description', $page->getDescription());
        $this->addMeta('keywords', $page->getKeywords());
    }

    public function slotBodyDataAttribute() {
        return 'tag-category';
    }

    public function slotSidebar() {
        return $this->render('tag/_sidebar_category', $this->params);
    }

    public function slotContentHead() {
        // заголовок контента страницы
        if (!$this->hasParam('title')) {
            $this->setParam('title', null);
        }
        // навигация
        if (!$this->hasParam('breadcrumbs')) {
            $this->setParam('breadcrumbs', []);
        }

        return $this->render('_contentHead', $this->params);
    }

    /**
     * @param \Model\Page\Entity $page
     */
    private function applySeoPattern(\Model\Page\Entity $page) {
        $dataStore = \App::dataStoreClient();
        $shopScript = \App::shopScriptClient();

        /** @var $category \Model\Product\Category\Entity */
        $category = $this->getParam('category') instanceof \Model\Product\Category\Entity ? $this->getParam('category') : null;
        if (!$category) {
            return;
        }

        $region = \App::user()->getRegion();

        $seoTemplate = null;

        // токены категорий
        $categoryTokens = [];
        foreach ($category->getAncestor() as $iCategory) {
            $categoryTokens[] = $iCategory->getToken();
        }
        $categoryTokens[] = $category->getToken();

        $shopScriptSeo = $this->getParam('shopScriptSeo');
        while(!empty($shopScriptSeo['redirect']['token'])) {
            $shopScript->addQuery('category/get-seo', [
                    'slug' => $shopScriptSeo['redirect']['token'],
                    'geo_id' => \App::user()->getRegion()->getId(),
                ], [], function ($data) use (&$shopScriptSeo) {
                if($data && is_array($data)) $shopScriptSeo = reset($data);
            });
            $shopScript->execute();
        }

        $seoTemplate = array_merge([
            'title'       => null,
            'description' => null,
            'keywords'    => null,
        ], $shopScriptSeo);

        // данные для шаблона
        $patterns = [
            'категория' => [$category->getName()],
            'город'     => [$region->getName()],
            'сайт'      => null,
        ];

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