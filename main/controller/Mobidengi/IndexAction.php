<?php

namespace Controller\Mobidengi;

class IndexAction {
    public function execute(\Http\Request $request) {
        $page = new \View\Mobidengi\IndexPage();
        return new \Http\Response($page->show());
    }
}
