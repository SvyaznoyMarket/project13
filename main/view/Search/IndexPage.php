<?php

namespace View\Search;

class IndexPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        $this->setParam('breadcrumbs', []);

        /** @var $productPager \Iterator\EntityPager */
        $productPager = $this->getParam('productPager') instanceof \Iterator\EntityPager ? $this->getParam('productPager') : null;

        // seo: title
        if (!$this->hasParam('title')) {
            $title = 'Вы искали ' . $this->escape($this->getParam('searchQuery')) . '';
            if ($productPager && ($productPager->getPage() > 1)) {
                $title .= ' – ' . $productPager->getPage();
            }
            $title .= ' – Enter.ru';

            $this->setTitle($title);
            $this->setParam('title', $title);
        }
    }

    public function slotBodyDataAttribute() {
        return 'product_catalog search';
    }

    public function slotContent() {
        $this->setParam('request', \App::request());

        return $this->render('search/page-index-new', $this->params);
    }

    public function slotContentHead() {
        // заголовок контента страницы
        if (!$this->hasParam('title')) {
            $this->setParam('title', null);
        }

        return $this->render('_contentHead', array_merge($this->params, ['title' => null])); // TODO: осторожно, костыль
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
            $this->getPrevNextRelLinks(['q' => \App::request()->query->get('q')]);
    }

    /**
     * @return string
     */
    protected function getSort() {
        return \App::helper()->getCurrentSort();
    }
}
