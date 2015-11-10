<?php


namespace Controller\User\Enterprize;

use EnterApplication\CurlTrait;
use EnterQuery as Query;

class IndexAction extends \Controller\User\PrivateAction {
    use CurlTrait;

    public function execute(\Http\Request $request) {
        $curl = $this->getCurl();

        $region = \App::user()->getRegion();

        $page = new \View\User\Enterprize\IndexPage();

        return new \Http\Response($page->show());
    }
}