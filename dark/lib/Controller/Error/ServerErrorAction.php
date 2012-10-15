<?php

namespace Controller\Error;

class ServerErrorAction {
    public function execute(\Exception $e) {
        $page = new \View\Error\IndexPage();

        $page->setParam('title', 'Ошибка 500');
        $page->setParam('message', 'Что-то поломалось...');

        return new \Http\Response($page->show(), 500);
    }
}
