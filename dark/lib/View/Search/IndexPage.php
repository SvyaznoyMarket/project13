<?php

namespace View\Search;

class IndexPage extends \View\DefaultLayout {
    public function prepare() {
        /** @var $productPager \Iterator\EntityPager */
        $productPager = $this->getParam('productPager') instanceof \Iterator\EntityPager ? $this->getParam('productPager') : null;

        if (!$this->hasParam('title')) {
            $title = 'Вы искали “' . $this->getParam('searchQuery') . '”';
            if ($productPager && ($productPager->getPage() > 1)) {
                $title .= ' – ' . $productPager->getPage();
            }
            $title .= ' – Enter.ru';

            $this->setTitle($title);
            $this->setParam('title', trim($this->render('search/_title', array(
                'searchQuery' => $this->getParam('searchQuery'),
                'meanQuery'   => $this->getParam('meanQuery'),
                'forceMean'   => $this->getParam('forceMean'),
                'count'       => $productPager ? $productPager->count() : 0,
            ))));
        }
    }

    public function slotBodyDataAttribute() {
        return 'product_catalog';
    }

    public function slotContent() {
        $this->setParam('request', \App::request());

        return $this->render('search/page-index', $this->params);
    }

    public function slotSidebar() {
        $this->setParam('limit', 8);

        if (!(bool)$this->getParam('categories')) {
            return  '';
        }

        return $this->render('search/_sidebar', $this->params);
    }
}
