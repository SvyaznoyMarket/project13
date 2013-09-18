<?php

namespace Controller\Content;

class Action {

    public function execute(\Http\Request $request, $token) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::contentClient();

        $content = $client->query($token, [], false, \App::config()->coreV2['retryTimeout']['huge']);

        if (!(bool)$content) {
            throw new \Exception\NotFoundException();
        }

        $page = new \View\Content\IndexPage();
        $page->setTitle($content['title']);
        $page->setParam('token', $token);

        switch ($token) {
            case 'service_ha':
            case 'services_ha':
                $htmlContent = preg_replace('/<script.*script>/sm', '', $content['content']);
                $serviceJson = $this->getServiceJson();
                $page->setParam('data', $serviceJson);
                break;
            default:
                $htmlContent = $content['content'];
                $page->setParam('data', []);
                break;
        }

        $page->setParam('htmlContent', $htmlContent);

        //нужно для увеличения отступа от заголовкой и строки поика
        $page->setParam('extendedMargin', true);
        if (!(bool)$content['layout'])
        {
            $page->setParam('title', $content['title']);
            //нужно, чтобы после заголовка и строки поиска была линия
            $page->setParam('hasSeparateLine', true);
        }
        else {
            $page->setParam('breadcrumbs', null);
        }

        return new \Http\Response($page->show());
    }


    private function getServiceJson() {
        \App::logger()->debug('Exec ' . __METHOD__);

        $dataStore = \App::dataStoreClient();

        $serviceJson = [];
        $dataStore->addQuery('service_ha/*.json', [], function ($data) use (&$serviceJson) {
            if($data) $serviceJson = $data;
        });
        $dataStore->execute();

        return $serviceJson;
    }

}