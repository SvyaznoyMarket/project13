<?php

namespace Controller\Cart;

class IndexAction {
    public function execute() {
        $page = new \View\Cart\IndexPage();

        return new \Http\Response($page->show());
    }
}