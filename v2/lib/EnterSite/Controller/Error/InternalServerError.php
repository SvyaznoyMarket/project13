<?php

namespace EnterSite\Controller\Error;

use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\MustacheRendererTrait;
//use EnterSite\Repository;
//use EnterSite\Model;
//use EnterSite\Model\Page\Error\InternalServerError as Page;

class InternalServerError {
    use ConfigTrait, MustacheRendererTrait {
        ConfigTrait::getConfig insteadof MustacheRendererTrait;
    }

    /**
     * @param Http\Request $request
     * @return Http\Response
     */
    public function execute(Http\Request $request = null) {
        $response = ($request && $request->isXmlHttpRequest()) ? new Http\JsonResponse() : new Http\Response();
        $response->statusCode = Http\Response::STATUS_INTERNAL_SERVER_ERROR;

        $page = [
            'error' => [
                'message' => isset(error_get_last()['message']) ? error_get_last()['message'] : 'Неизвестная ошибка',
            ],
        ];

        if ($response instanceof Http\JsonResponse) {
            $response->data['error'] = [
                'code'    => 500,
                'message' => 'Internal Server Error',
            ];
        } else {
            // рендер
            $renderer = $this->getRenderer();
            $renderer->setPartials([
                'content' => 'page/error',
            ]);
            $response->content = $renderer->render('page/error', $page);
        }

        return $response;
    }
}