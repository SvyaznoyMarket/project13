<?php

namespace Controller\Main;

class IndexAction {
    public function execute() {
        $page = new \View\Main\IndexPage();

        return new \Http\Response($page->show());
    }
}
