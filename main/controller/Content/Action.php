<?php

namespace Controller\Content;

class Action {

    public function execute(\Http\Request $request, $token) {
        //\App::logger()->debug('Exec ' . __METHOD__);

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
                $htmlContent = str_replace('table class="bServicesTable"', 'table id="bServicesTable" class="bServicesTable"', $htmlContent); // TODO: осторожно, говнокод
                $serviceJson = $this->getServiceJson();
                $page->setParam('data', $serviceJson);
                break;
            default:
                $htmlContent = $content['content'];
                $page->setParam('data', []);
                break;
        }

        $htmlContent = str_replace('<script src="https://content.enter.ru/wp-includes/js/jquery/jquery.js" type="text/javascript"></script>', '', $htmlContent);

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
        //\App::logger()->debug('Exec ' . __METHOD__);

        $dataStore = \App::dataStoreClient();

        $serviceJson = [];
        $dataStore->addQuery('service_ha/*.json', [], function ($data) use (&$serviceJson) {
            if (is_array($data)) $serviceJson = $data;
        });
        $dataStore->execute();

        $firstData = [];
        foreach ($serviceJson as $key => $item) {
            if (('Москва и МО' == $key) || ('Санкт-Петербург' == $key)) {
                $firstData[$key] = $item;
                unset($serviceJson[$key]);
            }
        }

        $serviceJson = array_reverse(array_merge($firstData, $serviceJson), true);

        return $serviceJson;
    }

}