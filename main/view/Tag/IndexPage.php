<?php

namespace View\Tag;

class IndexPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        /** @var $tag \Model\Tag\Entity */
        $tag = $this->getParam('tag') instanceof \Model\Tag\Entity ? $this->getParam('tag') : null;
        if (!$tag) {
            return;
        }

        // Выбранная категория
        $selectedCategory = $this->getParam('selectedCategory');

        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = [];
            $breadcrumbs[] = array(
                'name' => 'Теги &rsaquo; ' . $tag->getName(),
                'url'  => \App::router()->generate('tag', array('tagToken' => $tag->getToken())),
            );

            if ($selectedCategory) {
                $breadcrumbs[] = array(
                    'name' => $selectedCategory->getName(),
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


        // Заголовок title страницы
        $pageTitle = 'Тег &laquo;'.$tag->getName().'&raquo;';
        if ($selectedCategory) {
            $pageTitle .= ' &ndash; ' . $selectedCategory->getName();
        }
        $this->setParam('pageTitle', $pageTitle);

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
        return 'product_catalog';
    }

    public function slotContent() {
        return $this->render('tag/page-index-new', $this->params);
    }

    public function slotContentHead() {
        $ret = '';
        $categoryData = null;

        $category = $this->getParam('category');
        if (!$category) $category = $this->getParam('selectedCategory');

        if ($category) $categoryData = $this->tryRender('product-category/_categoryData', array('page' => $this, 'category' => $category));
        if ($categoryData) $ret .= $categoryData;

        return $ret;
    }

    public function slotUserbarContent() {
        return $this->render('product-category/_userbarContent', [
            'category'  => $this->getParam('category') instanceof \Model\Product\Category\Entity ? $this->getParam('category') : null,
        ]);
    }

    public function slotUserbarContentData() {
        return [
            'target' => '#productCatalog-filter-form',
        ];
    }

    public function slotConfig() {
        return $this->tryRender('_config', ['config' => [
            'location' => ['listing'],
        ]]);
    }
}