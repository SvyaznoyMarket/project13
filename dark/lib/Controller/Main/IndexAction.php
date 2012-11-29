<?php

namespace Controller\Main;

class IndexAction {
    public function execute() {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        // внимание! опасный трюк: если есть кука региона избавляемся от запроса в ядро
        $region = $user->getRegionId() ? new \Model\Region\Entity(array('id' => $user->getRegionId())) : $user->getRegion();

        // подготовка 1-го пакета запросов

        // запрашиваем рутовые категории
        $rootCategories = array();
        \RepositoryManager::getProductCategory()->prepareRootCollection($region, function($data) use(&$rootCategories) {
            foreach ($data as $item) {
                $rootCategories[] = new \Model\Product\Category\Entity($item);
            }
        });

        // запрашиваем баннеры
        $bannerData = array();
        \RepositoryManager::getBanner()->prepareCollection($region, function ($data) use (&$bannerData) {
            $timeout = \App::config()->banner['timeout'];
            $urls = \App::config()->banner['url'];

            foreach ($data as $i => $item) {
                $item = array(
                    'id'    => isset($item['id']) ? (int)$item['id'] : null,
                    'name'  => isset($item['name']) ? (string)$item['name'] : null,
                    'url'   => isset($item['url']) ? (string)$item['url'] : null,
                    'image' => isset($item['media_image']) ? (string)$item['media_image'] : null,
                );

                if (empty($item['url']) || empty($item['image'])) continue;

                $bannerData[] = array(
                    'alt'   => $item['name'],
                    'imgs'  => $item['image'] ? ($urls[0] . $item['image']) : null,
                    'imgb'  => $item['image'] ? ($urls[1] . $item['image']) : null,
                    'url'   => $item['url'],
                    't'     => $i > 0 ? $timeout : $timeout + 4000,
                    'ga'    => $item['id'] . ' - ' . $item['name'],
                );
            }
        });

        // выполнение 1-го пакета запросов
        $client->execute();

        $page = new \View\Main\IndexPage();
        $page->setParam('bannerData', $bannerData);
        $page->setParam('rootCategories', $rootCategories);

        return new \Http\Response($page->show());
    }
}
