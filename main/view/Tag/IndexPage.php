<?php

namespace View\Tag;

class IndexPage extends \View\DefaultLayout {
    public function prepare() {
        /** @var $tag \Model\Tag\Entity */
        $tag = $this->getParam('tag') instanceof \Model\Tag\Entity ? $this->getParam('tag') : null;
        if (!$tag) {
            return;
        }

        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = [];
            $breadcrumbs[] = array(
                'name' => 'Теги &rsaquo; ' . $tag->getName(),
                'url'  => \App::router()->generate('tag', array('tagToken' => $tag->getToken())),
            );

            /** @var $category \Model\Product\Category\Entity */
            $category = $this->getParam('category') instanceof \Model\Product\Category\Entity ? $this->getParam('category') : null;
            if ($category) {
                $breadcrumbs[] = array(
                    'name' => $category->getName(),
                    'url'  => null, // потому что последний элемент ;)
                );
            }

            $this->setParam('breadcrumbs', $breadcrumbs);
        }

        $regionName = \App::user()->getRegion()->getName();

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

        try {
            $this->applySeoPattern($page, $tag);
        } catch (\Exception $e) {
            \App::logger()->error($e);
        }

        $this->setTitle($page->getTitle());
        $this->addMeta('description', $page->getDescription());
        $this->addMeta('keywords', $page->getKeywords());
    }

    public function slotBodyDataAttribute() {
        return 'product_catalog';
    }

    public function slotContent() {
        return $this->render('tag/page-index', $this->params);
    }

    public function slotSidebar() {
        if (!(bool)$this->getParam('categories')) {
            return  '';
        }

        return $this->render('tag/_sidebar', array_merge($this->params, array(
            'selectedCategory' => $this->getParam('category'),
            'limit'            => 8,
        )));
    }

    private function applySeoPattern(\Model\Page\Entity $page, \Model\Tag\Entity $tag) {
        $dataStore = \App::dataStoreClient();

        if (!$tag) {
            return;
        }

        $region = \App::user()->getRegion();

        $seoTemplate = null;

        $dataStore->addQuery(sprintf('seo/tag/%s.json', $tag->getToken()), [], function ($data) use (&$seoTemplate) {
            $seoTemplate = array_merge([
                'title'       => null,
                'description' => null,
                'keywords'    => null,
            ], $data);
        });

        // данные для шаблона
        $patterns = [
            'тэг' => [$tag->getName()],
            'город'     => [$region->getName()],
            'сайт'      => null,
        ];
        $dataStore->addQuery(sprintf('inflect/tag/%s.json', $tag->getToken()), [], function($data) use (&$patterns) {
            if ($data) $patterns['тэг'] = $data;
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