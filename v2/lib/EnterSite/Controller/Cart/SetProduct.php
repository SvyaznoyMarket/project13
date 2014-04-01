<?php

namespace EnterSite\Controller\Cart;

use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\Controller;

class SetProduct {
    use ConfigTrait;
    use LoggerTrait {
        ConfigTrait::getConfig insteadof LoggerTrait;
    }

    public function execute() {


        // FIXME: заглушка
        return new Http\JsonResponse([
            'success' => true,
        ]);
    }
}