<?php

namespace Controller\Compare;

class CompareAction {

    public function execute() {

        $page = new \View\Compare\CompareLayout();
        return new \Http\Response($page->show());
    }

} 