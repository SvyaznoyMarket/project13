<?php

namespace Controller\Mobidengi;

class IndexAction {
    public function execute() {
        \App::logger()->debug('Exec ' . __METHOD__);

        $page = new \View\Mobidengi\IndexPage();

        return new \Http\Response($page->show());
    }
}
