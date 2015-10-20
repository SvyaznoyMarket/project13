<?php


namespace Controller\User\Address;

use EnterApplication\CurlTrait;
use EnterQuery as Query;

class IndexAction extends \Controller\User\PrivateAction {
    use CurlTrait;

    public function execute(\Http\Request $request) {
        $curl = $this->getCurl();

        $page = new \View\User\Address\IndexPage();

        return new \Http\Response($page->show());
    }
}