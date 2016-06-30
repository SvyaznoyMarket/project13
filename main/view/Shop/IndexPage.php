<?php

namespace View\Shop;

use Model\Point\ScmsPoint;

class IndexPage extends \View\DefaultLayout {
    protected $layout  = 'layout-twoColumn';

    protected function prepare() {
        /** @var $points ScmsPoint[] */
        $points = $this->params['points'];
        $partners = $partnersBySlug = $this->params['partners'];

        foreach ($points as $key => $point) {
            if (!$point->latitude || !$point->longitude) {
                unset($points[$key]);
            }
        }

        // Фильтруем партнеров, оставляя только тех, которые есть в списке точек
        $existingPartners = array_unique(array_map(function(ScmsPoint $point){ return $point->partner->slug; }, $points));
        $partners = array_intersect_key($partners, array_fill_keys($existingPartners, null));

        // Сортируем
        $partnerOrder = ['enter', 'euroset', 'pickpoint', 'hermes', 'formula-m', 'svyaznoy'];
        usort($partners, function ($a, $b) use ($partnerOrder){ return array_search($a['slug'], $partnerOrder) > array_search($b['slug'], $partnerOrder);});

        // Создаем JSON для ObjectManager
        $this->setParam('objectManagerData', $this->getPointsForObjectManager($points));

        $this->params['partners'] = $partners;
        $this->setParam('partnersBySlug', $partnersBySlug);
        $this->setParam('points', $points);
    }


    public function slotBodyDataAttribute() {
        return 'shop';
    }

    public function slotContent() {
        return $this->render('shop/content', $this->params);
    }

    public function slotSidebar() {
        return $this->getParam('sidebarHtml');
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
                'id'    => $point->ui,
                'geometry'  => [
                    'type'          => 'Point',
                    'coordinates'   => [$point->latitude, $point->longitude]
                ],
                'options'   => [
                    'iconImageHref' => $point->icon
                ],
                'properties'    => [
                    'eUid'     => $point->ui,
                    'ePartner' => $point->partner->slug
                ]
            ];
        }

        return $result;
    }


}