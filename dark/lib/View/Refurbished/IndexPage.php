<?php

namespace View\Refurbished;

class IndexPage extends \View\DefaultLayout {
    public function prepare() {

        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = array();
            $breadcrumbs[] = array(
                'name' => 'Уцененные товары оптом',
                'url'  => \App::router()->generate('refurbished'),
            );

            $this->setParam('breadcrumbs', $breadcrumbs);
        }

        // seo: title
        if (!$this->hasParam('title')) {
            $this->setTitle('Уцененные товары оптом – Enter.ru');
            $this->setParam('title', 'Уцененные товары оптом');
        }
    }

    public function slotContent() {
        return $this->render('refurbished/page-index', $this->params);
    }

}