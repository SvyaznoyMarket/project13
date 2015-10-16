<?php

namespace View\Subscribe\Friend;

class ShowPage extends \View\DefaultLayout {
    /** @var string */
    protected $layout  = 'layout-landing';

    public function prepare() {
        $this->setTitle('Дружить с нами выгодно и интересно!');
        $this->setParam('title', 'Дружить с нами выгодно и интересно!');
        $this->addMeta('description', 'Enter - новый способ покупать. Любой из ' . \App::config()->product['totalCount'] . ' товаров нашего ассортимента можно купить где угодно, как угодно и когда угодно. Наша миссия: дарить время для настоящего. Честно. С любовью. Как для себя.');
    }

    public function slotBodyDataAttribute() {
        return 'infopage';
    }

    public function slotContent() {
        return $this->render('subscribe/friend/page-show', $this->params);
    }
}
