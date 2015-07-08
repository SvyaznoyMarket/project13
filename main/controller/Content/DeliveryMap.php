<?php

namespace Controller\Content;


use View\Content\DeliveryMapPage;
use Model\Point\ScmsPoint;

class DeliveryMap {

    public function execute(\Http\Request $request) {

        $page = new DeliveryMapPage();
        $points = null;
        $partners = null;

        // Используем страницу контактов чтобы получить меню
        $contentClient = \App::contentClient();
        $scmsClient = \App::scmsClient();
        $contentClient->addQuery('contacts', [], function($data) use (&$sidebar) {
            $content = (string)@$data['content'];
            $content = str_replace(array("\r", "\n"), '', $content);
            preg_match('/\<!-- Menu start --\>(.*)\<!-- Menu end --\>/mu', $content, $matches);
            $sidebar = @$matches[1];
        });

        $scmsClient->addQuery('api/point/get', $this->getFilters($request), [],
            function ($data) use (&$points, &$partners) {
                if (isset($data['points']) && is_array($data['points'])) $points = array_map(function(array $pointData) { return new ScmsPoint($pointData); }, $data['points']);
                if (isset($data['partners'])) foreach ($data['partners'] as $partner) { $partners[$partner['slug']] = $partner; }
            }
        );

        \App::curl()->execute();

        $page->setParam('sidebar', $sidebar);
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