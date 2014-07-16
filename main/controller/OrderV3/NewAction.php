<?php

namespace Controller\OrderV3;

class NewAction {

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $page = new \View\OrderV3\NewPage();

        return new \Http\Response($page->show());
    }
}