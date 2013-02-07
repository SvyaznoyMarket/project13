<?php

namespace View\Search;

class IndexPage extends \View\DefaultLayout {
    public function prepare() {
        /** @var $productPager \Iterator\EntityPager */
        $productPager = $this->getParam('productPager') instanceof \Iterator\EntityPager ? $this->getParam('productPager') : null;

        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = [];
            $breadcrumbs[] = array(
                'name' => 'Поиск (' . $this->escape($this->getParam('searchQuery')) . ')',
                'url'  => \App::router()->generate('search', array('q' => $this->getParam('searchQuery'))),
            );

            $this->setParam('breadcrumbs', $breadcrumbs);
        }

        // seo: title
        if (!$this->hasParam('title')) {
            $title = 'Вы искали “' . $this->escape($this->getParam('searchQuery')) . '”';
            if ($productPager && ($productPager->getPage() > 1)) {
                $title .= ' – ' . $productPager->getPage();
            }
            $title .= ' – Enter.ru';

            $this->setTitle($title);
            $this->setParam('title', trim($this->render('search/_title', array(
                'searchQuery' => $this->getParam('searchQuery'),
                'meanQuery'   => $this->getParam('meanQuery'),
                'forceMean'   => $this->getParam('forceMean'),
                'count'       => $this->getParam('productCount'),
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
        if (!(bool)$this->getParam('categories')) {
            return  '';
        }

        return $this->render('search/_sidebar', array_merge($this->params, array(
            'limit' => 8,
        )));
    }
}
