<?php

namespace controller\Supplier;


use Http\Response;
use View\Supplier\CabinetPage;

class CabinetAction {

    public function execute() {
        $page = new CabinetPage();
        return new Response($page->show());
    }

}