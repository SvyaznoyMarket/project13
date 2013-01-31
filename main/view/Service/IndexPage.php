<?php

namespace View\Service;

class IndexPage extends \View\DefaultLayout {
    protected $layout = 'layout-oneColumn';

    public function prepare() {
        $this->helper = new Helper();

        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = [];
            $breadcrumbs[] = array(
                'name' => 'Услуги F1',
                'url'  => null,
            );

            $this->setParam('breadcrumbs', $breadcrumbs);
        }

        // seo: title
        $this->setTitle('F1 - Услуги F1 - Enter.ru');
        $this->setParam('title', 'Услуги F1');
    }

    public function slotBodyDataAttribute() {
        return 'product_catalog';
    }

    public function slotContent() {
        return $this->render('service/page-index', $this->params);
    }
}