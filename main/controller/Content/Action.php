<?php

namespace Controller\Content;

use Templating\Helper;

class Action {

    public function execute(\Http\Request $request, $token) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::scmsClient();

        $content = null;
        $data = [
            'regionName' => \App::user()->getRegion()->getName(),
        ];

        $client->addQuery(
            'api/static-page',
            [
                'token' => [$token],
            ],
            [],
            function($response) use (&$content, &$token) {
                if (!isset($response['pages'][0]['content'])) return;

                $content = $response['pages'][0];
                $content['content'] = str_replace('<script src="https://content.enter.ru/wp-includes/js/jquery/jquery.js" type="text/javascript"></script>', '', $content['content']);
            }
        );

        if ($token === 'service_ha')
            \App::dataStoreClient()->addQuery('service_ha/*.json', [], function ($response) use (&$data) {
            if (is_array($response)) {
                $data['services'] = $response;

                $toBegin = [];
                foreach ($data['services'] as $key => $item) {
                    if ('Москва и МО' === $key || 'Санкт-Петербург' === $key) {
                        $toBegin[$key] = $item;
                        unset($data['services'][$key]);
                    }
                }

                $data['services'] = array_merge($toBegin, $data['services']);
            }
        });

        $client->execute();

        if (!$content) {
            throw new \Exception\NotFoundException();
        }

        if ($token === 'service_ha') {
            $helper = new Helper();
            $content['content'] = str_replace('%regions%', implode("\n", array_map(function($region) use(&$helper) { return '<option value="' . $helper->escape($region) . '">' . $helper->escape($region) . '</option>'; }, array_keys($data['services']))), $content['content']);
        }

        if ($request->isXmlHttpRequest() && $request->get('ajax')) {
            return new \Http\JsonResponse([
                'title' => $content['title'],
                'content' => $content['content'],
            ]);
        } else {
            $page = new \View\Content\IndexPage();
            $page->setTitle($content['title']);

            $page->setParam('data', $data);
            $page->setParam('htmlContent', $content['content']);
            $page->setParam('imageUrl', isset($content['image_url']) ? $content['image_url'] : '');
            $page->setParam('description', isset($content['description']) ? $content['description'] : '');
            $page->setParam('token', $token);
            //нужно для увеличения отступа от заголовкой и строки поика
            $page->setParam('extendedMargin', true);
            $page->setParam('title', $content['title']);
            //нужно, чтобы после заголовка и строки поиска была линия
            $page->setParam('hasSeparateLine', true);
            return new \Http\Response($page->show());
        }
    }
}