<?php

namespace View\Shop;

class RegionPage extends \View\DefaultLayout {
    protected $layout = 'layout-oneColumn';

    public function prepare() {
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = [];
            $breadcrumbs[] = array(
                'name' => 'Все магазины Enter',
                'url'  => null,
            );

            $this->setParam('breadcrumbs', $breadcrumbs);
        }

        // seo: title
        $title = 'Все магазины';
        $this->setTitle($title);
        if (!(\App::request()->get('route') == 'tchibo.where_buy')) $this->setParam('title', $title);
    }

    public function slotBodyDataAttribute() {
        return 'shop';
    }

    public function slotContent() {
        return $this->render('shop/page-region', $this->params);
    }
}