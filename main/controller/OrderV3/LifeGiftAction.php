<?php


namespace Controller\OrderV3;


class LifeGiftAction extends OrderV3 {

    public function execute(\Http\Request $request) {
        $page = new \View\OrderV3\LifeGiftPage();
        return new \Http\Response($page->show());
    }

} 