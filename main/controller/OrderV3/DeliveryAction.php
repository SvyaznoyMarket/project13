<?php

namespace Controller\OrderV3;

class DeliveryAction {

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $page = new \View\OrderV3\DeliveryPage();

        return new \Http\Response($page->show());
    }
}