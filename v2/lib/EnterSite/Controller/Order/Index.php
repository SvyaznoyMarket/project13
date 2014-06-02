<?php

namespace EnterSite\Controller\Order;

use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\Controller;
use EnterSite\SessionTrait;
use EnterSite\Repository;

class Index {
    use ConfigTrait, LoggerTrait, SessionTrait {
        ConfigTrait::getConfig insteadof LoggerTrait, SessionTrait;
        LoggerTrait::getLogger insteadof SessionTrait;
    }

    /**
     * @param Http\Request $request
     * @return Http\Response
     */
    public function execute(Http\Request $request) {
        $url = strtr($request->getSchemeAndHttpHost(), [
            'm.'    => '',
            ':8080' => '', //FIXME: костыль для nginx-а
        ]) . '/orders/new';

        return (new Controller\Redirect())->execute($url, 302);
    }
}