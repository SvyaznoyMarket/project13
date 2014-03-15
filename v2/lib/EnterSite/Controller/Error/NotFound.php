<?php

namespace EnterSite\Controller\Error;

use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\MustacheRendererTrait;
use EnterSite\Repository;
use EnterSite\Model;
//use EnterSite\Model\Page\Error\NotFound as Page;

class NotFound {
    use ConfigTrait;
    use MustacheRendererTrait {
        ConfigTrait::getConfig insteadof MustacheRendererTrait;
    }

    /**
     * @return Http\Response
     */
    public function execute() {
        $response = new Http\Response('Страница не найдена');
        $response->statusCode = Http\Response::STATUS_NOT_FOUND;

        // TODO: использование шаблона
        return $response;
    }
}