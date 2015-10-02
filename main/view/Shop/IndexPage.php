<?php

namespace View\Shop;

use Model\Point\ScmsPoint;

class IndexPage extends \View\DefaultLayout {
    protected $layout  = 'layout-twoColumn';

    protected function prepare() {
        /** @var $points ScmsPoint[] */
        $points = $this->params['points'];
        $partners = $partnersBySlug = $this->params['partners'];

        $this->filterPoints($points);

        // Фильтруем партнеров, оставляя только тех, которые есть в списке точек
        $existingPartners = array_unique(array_map(function(ScmsPoint $point){ return $point->partner->slug; }, $points));
        $partners = array_intersect_key($partners, array_fill_keys($existingPartners, null));

        // Сортируем
        $partnerOrder = ['enter', 'euroset', 'pickpoint', 'hermes', 'svyaznoy'];
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

    /** Фильтруем точки вручную (вынужденный говнокод)
     * SITE-5819
     * @param $points ScmsPoint[]
     */
    private function filterPoints(&$points) {

        $svyaznoyList = ['4050800','4010800','4020000','4020800','4060800','7060800','7010403','7071800','7080000','2030000','5010000','5091900','5050800','5030000','1021900','3010000','3050000','2010000','11040000','11140800','11150000','11160000'];

        $points = array_filter($points, function (ScmsPoint $point) use ($svyaznoyList) {
            return $point->partner->slug != ScmsPoint::PARTNER_SLUG_SVYAZNOY ? true :
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