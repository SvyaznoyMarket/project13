<?php

namespace Controller\Content;


use View\Content\DeliveryMapPage;
use Model\Point\ScmsPoint;

class DeliveryMap {

    public function execute(\Http\Request $request) {
        $page = new DeliveryMapPage();
        /** @var $points ScmsPoint[] */
        $points = [];
        $partners = [];

        $scmsClient = \App::scmsClient();
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
                if (isset($data['partners'])) {
                    foreach ($data['partners'] as $partner) {
                        $partners[$partner['slug']] = $partner;
                    }
                }

                if (isset($data['points']) && is_array($data['points'])) {
                    $points = array_map(function(array $pointData) use($partners) {
                        $point = new ScmsPoint($pointData);
                        if (!empty($point->group) && !empty($partners[$point->group->id])) {
                            $point->group = new \Model\Point\Group($partners[$point->group->id]);
                        }

                        return $point;
                    }, $data['points']);
                }
            }
        );

        \App::curl()->execute();

        $content = str_replace('<!--REGION_NAME-->', \App::user()->getRegion()->getName(), $content);
        $content = str_replace('<!--DELIVERY_LOGOS-->', \App::mustache()->render('content/delivery/logos', [
            'partners' => array_values(array_filter(array_map(function($partner) {
                if (empty($partner['slug'])) {
                    return null;
                }

                return ['slug' => $partner['slug']];
            }, $partners)))
        ]), $content);

        $this->filterPoints($points);

        call_user_func(function() use(&$partners, $points) {
            // Фильтруем партнеров, оставляя только тех, которые есть в списке точек
            $existingPartners = array_unique(array_map(function(ScmsPoint $point){ return $point->partner;}, $points));
            $partners = array_intersect_key($partners, array_fill_keys($existingPartners, null));

            // Сортируем
            $partnerOrder = ['enter', 'euroset', 'pickpoint', 'hermes', 'svyaznoy'];
            usort($partners, function ($a, $b) use ($partnerOrder){ return array_search($a['slug'], $partnerOrder) > array_search($b['slug'], $partnerOrder);});
        });

        $page->setParam('content', $content);
        $page->setParam('title', 'Магазины и точки самовывоза');
        $page->setParam('points', $points);
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

    /** Фильтруем точки вручную (вынужденный говнокод)
     * @param $points ScmsPoint[]
     */
    private function filterPoints(&$points) {

        $svyaznoyList = ['4050800','4010800','4020000','4020800','4060800','7060800','7010403','7071800','7080000','2030000','5010000','5091900','5050800','5030000','1021900','3010000','3050000','2010000','11040000','11140800','11150000','11160000'];

        $points = array_filter($points, function (ScmsPoint $point) use ($svyaznoyList) {
            return $point->partner != ScmsPoint::PARTNER_SLUG_SVYAZNOY ? true :
                false !== in_array($point->vendorId, $svyaznoyList);
        });
    }

    /** Формирование массива для Yandex Maps ObjectManager
     * @link https://tech.yandex.ru/maps/doc/jsapi/2.1/dg/concepts/object-manager/about-docpage/
     * @param $points ScmsPoint[]
     * @return array
     */
    private function getPointsForObjectManager($points){
        $result = [
            'type'      => 'FeatureCollection',
            'features'    => []
        ];

        foreach ($points as $point) {
            $result['features'][] = [
                'type'  => 'Feature',
                'id'    => $point->uid,
                'geometry'  => [
                    'type'          => 'Point',
                    'coordinates'   => [$point->latitude, $point->longitude]
                ],
                'options'   => [
                    'iconImageHref' => $point->icon
                ],
                'properties'    => [
                    'eUid'     => $point->uid,
                    'ePartner' => $point->partner
                ]
            ];
        }

        return $result;
    }
}