<?php

namespace EnterSite\Controller;

use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\MustacheRendererTrait;
use EnterSite\Repository;
use EnterSite\Model;
//use EnterSite\Model\Page\Error\InternalServerError as Page;

class InternalServerError {
    use ConfigTrait;
    use MustacheRendererTrait {
        ConfigTrait::getConfig insteadof MustacheRendererTrait;
    }

    /**
     * @return Http\Response
     */
    public function execute() {
        $response = new Http\Response('<pre>' . json_encode(error_get_last(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</pre>');
        $response->statusCode = Http\Response::STATUS_INTERNAL_SERVER_ERROR;

        // TODO: использование шаблона
        return $response;
    }
}