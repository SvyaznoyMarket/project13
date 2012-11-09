<?php

namespace Controller\User;

class OrderAction {
    public function execute(\Http\Request $request) {
        $page = new \View\User\OrderPage();

        return new \Http\Response($page->show());
    }
}