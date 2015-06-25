<?php

namespace View\Content;


use Model\Point\ScmsPoint;

class DeliveryMapPage extends \View\DefaultLayout {
//    protected $layout = 'layout-oneColumn';

    protected function prepare() {
        /** @var $points ScmsPoint[] */
        $points = $this->params['points'];
        $partners = $this->params['partners'];

        // Фильтруем партнеров, оставляя только тех, которые есть в списке точек
        $existingPartners = array_unique(array_map(function(ScmsPoint $point){ return $point->partner;}, $points));
        $partners = array_intersect_key($partners, array_fill_keys($existingPartners, null));

        // Сортируем
        $partnerOrder = ['enter', 'euroset', 'pickpoint', 'hermes', 'svyaznoy'];
        usort($partners, function ($a, $b) use ($partnerOrder){ return array_search($a['slug'], $partnerOrder) > array_search($b['slug'], $partnerOrder);});

        $this->params['partners'] = $partners;
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


}