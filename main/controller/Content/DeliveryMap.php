<?php

namespace Controller\Content;


use View\Content\DeliveryMapPage;
use Model\Point\ScmsPoint;

class DeliveryMap {

    public function execute(\Http\Request $request) {

        $page = new DeliveryMapPage();
        $points = null;
        $partners = null;

        $scmsClient = \App::scmsClient();
        $scmsClient->addQuery(
            'api/static-page',
            ['token' => ['menu']],
            [],
            function($data) use (&$sidebar) {
                if (isset($data['pages'][0]['content'])) {
                    $sidebar = (string)$data['pages'][0]['content'];
                }
            }
        );

        $scmsClient->addQuery(
            'api/static-page',
            ['token' => ['delivery']],
            [],
            function($data) use (&$content) {
                if (isset($data['pages'][0]['content'])) {
                    $content = (string)$data['pages'][0]['content'];
                }
            }
        );

        $scmsClient->addQuery('api/point/get', $this->getFilters($request), [],
            function ($data) use (&$points, &$partners) {
                if (isset($data['points']) && is_array($data['points'])) $points = array_map(function(array $pointData) { return new ScmsPoint($pointData); }, $data['points']);
                if (isset($data['partners'])) foreach ($data['partners'] as $partner) { $partners[$partner['slug']] = $partner; }
            }
        );

        \App::curl()->execute();

        $page->setParam('sidebar', $sidebar);
        $page->setParam('content', $content);
        $page->setParam('title', 'Магазины и точки самовывоза');
        $page->setParam('points', $points);
        $page->setParam('partners', $partners);
        return new \Http\Response($page->show());
    }

    private function getFilters(\Http\Request $request) {
        $filter = [
            'geo_id' => \App::user()->getRegionId()
        ];
        // Пока не будем фильтровать запросы к SCMS
        // if ($request->query->has('token')) $filter['partner_slugs'] = (array)$request->query->get('token');
        return $filter;
    }

}