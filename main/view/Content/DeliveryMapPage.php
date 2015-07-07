<?php

namespace View\Content;


use Model\Point\ScmsPoint;

class DeliveryMapPage extends \View\DefaultLayout {
    protected $layout = 'layout-twoColumn';

    protected function prepare() {
        /** @var $points ScmsPoint[] */
        $points = $this->params['points'];
        $partners = $partnersBySlug = $this->params['partners'];

        // Фильтруем партнеров, оставляя только тех, которые есть в списке точек
        $existingPartners = array_unique(array_map(function(ScmsPoint $point){ return $point->partner;}, $points));
        $partners = array_intersect_key($partners, array_fill_keys($existingPartners, null));

        // Сортируем
        $partnerOrder = ['enter', 'euroset', 'pickpoint', 'hermes', 'svyaznoy'];
        usort($partners, function ($a, $b) use ($partnerOrder){ return array_search($a['slug'], $partnerOrder) > array_search($b['slug'], $partnerOrder);});

        // Создаем JSON для ObjectManager
        $this->setParam('objectManagerData', $this->getPointsForObjectManager($points));

        $this->params['partners'] = $partners;
        $this->setParam('partnersBySlug', $partnersBySlug);
    }


    public function slotBodyDataAttribute() {
        return 'shop';
    }

    public function slotContent() {
        return $this->render('content/page-delivery', $this->params);
    }

    public function slotSidebar() {
        return $this->getParam('sidebar');
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