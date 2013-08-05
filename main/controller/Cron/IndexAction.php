<?php

namespace Controller\Cron;

class IndexAction {

    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $page = new \View\Cron\IndexPage();

        return new \Http\Response($page->show());
    }

}