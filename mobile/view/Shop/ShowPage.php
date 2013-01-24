<?php

namespace Mobile\View\Shop;

class ShowPage extends \Mobile\View\DefaultLayout {
    public function prepare() {
        $this->addJavascript('http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js');
        $this->addJavascript('http://api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU');
        $this->addJavascript('/js/mobile/storeItem.js');
    }

    /**
     * @return string
     */
    public function slotContent() {
        return $this->render('shop/page-show', $this->params);
    }
}