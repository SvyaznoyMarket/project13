<?php

namespace View\Search;

class IndexPage extends \View\DefaultLayout {
    public function prepare() {
        if (!$this->hasParam('title')) {
            $title = 'Вы искали “' . $this->getParam('searchQuery') . '”';
            if ($this->getParam('pageNum') > 1) {
                $title .= ' – ' . $this->getParam('pageNum');
            }

            $this->addMeta('title', $title);
            $this->setParam('title', trim($this->render('search/_title', array(
                'searchQuery' => $this->getParam('searchQuery'),
                'meanQuery'   => $this->getParam('meanQuery'),
                'forceMean'   => $this->getParam('forceMean'),
                'count'       => $this->getParam('productPager') instanceof \Iterator\EntityPager ? $this->getParam('productPager')->count() : 0,
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
