<?php

namespace Terminal\Controller\Main;

class IndexAction {
    public function execute() {
        \App::logger()->debug('Exec ' . __METHOD__);

        $page = new \Terminal\View\Main\IndexPage();

        return new \Http\Response($page->show());
    }
}
