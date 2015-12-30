<?php

namespace Controller\Content;

use Templating\Helper;

class Action {

    public function execute(\Http\Request $request, $token) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $region = \App::user()->getRegion();
        $client = \App::scmsClient();

        $contentPage = null;
        $data = [
            'regionName' => $region->getName(),
        ];

        $client->addQuery(
            'api/static-page',
            [
                'token' => [$token],
                'geo_town_id' => $region->id,
                'tags' => ['site-web'],
            ],
            [],
            function($response) use (&$contentPage, &$token) {
                if (!isset($response['pages'][0]['content']) || !trim($response['pages'][0]['content']) || empty($response['pages'][0]['available_by_direct_link'])) return;

                $contentPage = $response['pages'][0];
                $contentPage['content'] = str_replace('<script src="https://content.enter.ru/wp-includes/js/jquery/jquery.js" type="text/javascript"></script>', '', $contentPage['content']);
            }
        );

        $client->execute();

        if (!$contentPage) {
            throw new \Exception\NotFoundException();
        }

        if ($request->isXmlHttpRequest() && $request->get('ajax')) {
            return new \Http\JsonResponse([
                'title' => $contentPage['title'],
                'content' => $contentPage['content'],
            ]);
        } else {
            $page = new \View\Content\IndexPage();
            $page->setTitle($contentPage['title']);

            $page->setParam('data', $data);
            $page->setParam('htmlContent', $contentPage['content']);
            $page->setParam('imageUrl', isset($contentPage['image_url']) ? $contentPage['image_url'] : '');
            $page->setParam('description', isset($contentPage['description']) ? $contentPage['description'] : '');
            $page->setParam('token', $token);
            //нужно для увеличения отступа от заголовкой и строки поика
            $page->setParam('extendedMargin', true);
            $page->setParam('title', $contentPage['title']);
            //нужно, чтобы после заголовка и строки поиска была линия
            $page->setParam('hasSeparateLine', true);
            return new \Http\Response($page->show());
        }
    }
}