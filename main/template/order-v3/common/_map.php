<?php

return function(
    \Helper\TemplateHelper $helper,
    $id,
    \Model\OrderDelivery\Entity\Order $order,
    \Model\OrderDelivery\Entity $orderDelivery
) {
    /** @var \Model\OrderDelivery\Entity\Point\Shop[]|\Model\OrderDelivery\Entity\Point\Pickpoint[] $pointsById */

    $region = \App::user()->getRegion();

    $dataValue = [
        'latitude'  => $region->getLatitude(),
        'longitude' => $region->getLongitude(),
        'zoom'      => 10,
        'points'    => [],
    ];

    /** @var \Model\OrderDelivery\Entity\Point\Shop[]|\Model\OrderDelivery\Entity\Point\Pickpoint[]|\Model\OrderDelivery\Entity\Point\Svyaznoy[] $points */
    foreach ($order->possible_points as $token => $points) {
        foreach ($points as $point) {
            $p = $point['point'];
            $dataValue['points'][$token][] = [
                'id' => $p->id,
                'name' => $p->name,
                'address' => $p->address,
                'regtime' => $p->regtime,
                'latitude' => $p->latitude,
                'longitude' => $p->longitude,
                'marker'    => $orderDelivery->points[$token]->marker
            ];
        }
    }
    ?>

    <div id="<?= $id ?>" class="selShop popupFl" style="display: none;" data-block_name="<?= $order->block_name ?>">
        <div class="js-order-changePlace-close popupFl_clsr jsCloseFl" data-content="#<?= $id ?>"></div>

        <div class="selShop_hh">Выберите точку самовывоза</div>

        <!-- Новая верстка -->

        <div id="<?= $id . '-map' ?>" class="js-order-map selShop_r" data-value="<?= $helper->json($dataValue) ?>"></div>

    </div>

<? };