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
            }
        );

        /** @var \Model\Config\Entity[] $configParameters */
        $configParameters = [];
        $callbackPhrases = [];
        \RepositoryManager::config()->prepare(['site_call_phrases'], $configParameters, function(\Model\Config\Entity $entity) use (&$category, &$callbackPhrases) {
            if ('site_call_phrases' === $entity->name) {
                $callbackPhrases = !empty($entity->value['content_page']) ? $entity->value['content_page'] : [];
            }

            return true;
        });

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
            $page->setParam('title', $contentPage['title']);
            $page->setGlobalParam('callbackPhrases', $callbackPhrases);

            return new \Http\Response($page->show());
        }
    }
}