<?php

namespace EnterSite\Controller;

use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\Controller;

class Index {
    use ConfigTrait;
    use LoggerTrait {
        ConfigTrait::getConfig insteadof LoggerTrait;
    }

    public function execute() {
        // FIXME: заглушка
        return (new Controller\Redirect())->execute('/catalog/electronics/plansheti-3434', Http\Response::STATUS_FOUND);
    }
}