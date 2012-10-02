<?php

namespace View\Search;

class IndexPage extends \View\DefaultLayout {
    public function prepare() {
        if (!$this->hasParam('title')) {
            $title = 'Вы искали “' . $this->getParam('searchQuery') . '”';
            if ($this->getParam('pageNum') > 1) {
                $title .= ' – ' . $this->getParam('pageNum');
            }

            $this->setParam('title', $title);
            $this->addMeta('title', $title);
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
