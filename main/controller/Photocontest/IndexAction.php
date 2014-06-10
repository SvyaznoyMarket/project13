<?php

namespace Controller\Photocontest;

class IndexAction {
    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $curl = \App::coreClientV2();
        $user = \App::user();
        $region = $user->getRegion();

        // подготовка 1-го пакета запросов
        // FIXME
        $photos = [];
        $curl->addQuery(
            'product/get',
            [
                'select_type' => 'slug',
                'slug'        => 'planshetniy-kompyuter-wexler-tab-7id-8gb-3g-cherniy-2060101016995',
                'geo_id'      => $region->getId(),
            ],
            [],
            function($data) use (&$photos) {
                // Наполнение $photos
                $data = reset($data);
                $photos[] = [
                    'id' => $data['id'],
                ];
            }
        );

        // выполнение 1-го пакета запросов
        $curl->execute();
        // теперь переменная $photos наполнена данными

        // страница
        $page = new \View\Photocontest\IndexPage();
        $page->setParam('photos', $photos);

        return new \Http\Response($page->show());
    }
}
