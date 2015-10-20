<?php


namespace Controller\User\Message;

use EnterApplication\CurlTrait;
use EnterQuery as Query;

class IndexAction extends \Controller\User\PrivateAction {
    use CurlTrait;

    public function execute(\Http\Request $request) {
        $curl = $this->getCurl();

        $page = new \View\User\Message\IndexPage();

        return new \Http\Response($page->show());
    }
}