<?php

namespace Controller\Main;

class Action {
    public function execute() {
        \App::logger()->debug('Exec ' . __METHOD__);

        $page = new \View\Main\IndexPage();

        return new \Http\Response($page->show());
    }
}
