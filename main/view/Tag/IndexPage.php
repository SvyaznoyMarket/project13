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
                'name' => 'Теги &rsaquo; ' . $tag->name,
                'url'  => \App::router()->generateUrl('tag', array('tagToken' => $tag->token)),
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
                . $tag->name
                . ' - ' . $regionName
                . ' - ENTER.ru'
            );
        }

        // description
        if (!$page->getDescription()) {
            $page->setDescription(''
                . $tag->name
                . ' в ' . $regionName
                . ' с ценами и описанием.'
                . ' Купить в магазине Enter'
            );
        }

        $this->setTitle($page->getTitle());
        $this->addMeta('description', $page->getDescription());
    }

    public function slotBodyDataAttribute() {
        return 'product_catalog';
    }

    public function slotContent() {
        return $this->render('tag/page-index-new', $this->params);
    }

    public function slotContentHead() {
        return '';
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

    public function slotRelLink() {
        return
            parent::slotRelLink() . "\n" .
            $this->getPrevNextRelLinks();
    }

    /**
     * @return string
     */
    protected function getSort() {
        return \App::helper()->getCurrentSort();
    }
}