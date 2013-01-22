<?php

namespace Mobile\Controller\Main;

class IndexAction {
    public function execute() {
        \App::logger()->debug('Exec ' . __METHOD__);

        $page = new \Mobile\View\Main\IndexPage();

        return new \Http\Response($page->show());
    }
}
