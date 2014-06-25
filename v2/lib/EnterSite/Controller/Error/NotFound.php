<?php

namespace EnterSite\Controller\Error;

use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\MustacheRendererTrait;
use EnterSite\Controller;
use EnterSite\Repository;
use EnterSite\Model;
//use EnterSite\Model\Page\Error\NotFound as Page;

class NotFound {
    use ConfigTrait, MustacheRendererTrait {
        ConfigTrait::getConfig insteadof MustacheRendererTrait;
    }

    /**
     * @param Http\Request $request
     * @param string|null $message
     * @return Http\Response|Http\JsonResponse
     */
    public function execute(Http\Request $request, $message = null) {
        $response = ($request && $request->isXmlHttpRequest()) ? new Http\JsonResponse() : new Http\Response();
        $response->statusCode = Http\Response::STATUS_NOT_FOUND;

        $page = [
            'dataDebug' => $this->getConfig()->debugLevel ? 'true' : '',
            'error'     => [
                'message' => $message ?: 'Страница не найдена',
            ],
        ];

        if ($response instanceof Http\JsonResponse) {
            $response->data['error'] = [
                'code'    => 404,
                'message' => 'Not Found',
            ];
        } else {
            // рендер
            $renderer = $this->getRenderer();
            $renderer->setPartials([
                'content' => 'page/error',
            ]);
            $response->content = $renderer->render('page/error', $page);
        }

        //return $response;

        // FIXME: убрать
        //$url = str_replace('m.', '', $request->getSchemeAndHttpHost() . $request->getRequestUri());
        $url = strtr($request->getSchemeAndHttpHost(), [
            'm.'    => '',
            ':8080' => '', //FIXME: костыль для nginx-а
        ]) . $request->getRequestUri();

        return (new Controller\Redirect())->execute($url, 302);
    }
}