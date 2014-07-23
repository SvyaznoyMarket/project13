<?php

return function(
    \Helper\TemplateHelper $helper,
    $id,
    \Model\OrderDelivery\Entity\Order\Delivery $delivery,
    array $pointsById
) {
    /** @var \Model\OrderDelivery\Entity\Point\Shop[]|\Model\OrderDelivery\Entity\Point\Pickpoint[] $pointsById */

    $region = \App::user()->getRegion();

    $dataValue = [
        'latitude'  => $region->getLatitude(),
        'longitude' => $region->getLongitude(),
        'zoom'      => 10,
        'points'    => [],
    ];

    /** @var \Model\OrderDelivery\Entity\Point\Shop[]|\Model\OrderDelivery\Entity\Point\Pickpoint[] $points */
    $points = [];
    foreach ($delivery->point->possible_point_ids as $pointId) {
        $point = isset($pointsById[$pointId]) ? $pointsById[$pointId] : null;
        if (!$point) continue;

        $points[] = $point;
        $dataValue['points'][] = [
            'id'        => $point->id,
            'name'      => $point->name,
            'address'   => $point->address,
            'latitude'  => $point->latitude,
            'longitude' => $point->longitude,
        ];
    }
?>

<div id="<?= $id ?>" class="selShop popupFl" style="display: none;">
    <div class="js-order-changePlace-close popupFl_clsr" data-content="#<?= $id ?>"></div>

    <div class="selShop_h">
        <div class="selShop_tab selShop_tab-act">Магазины в Москве</div>
        <div class="selShop_tab">Pick point</div>
    </div>

    <div class="selShop_l">
        <ul class="shopLst">
        <? foreach ($points as $point): ?>
            <? $subway = (isset($point->subway) && isset($point->subway[0])) ? $point->subway[0] : null ?>

            <li class="shopLst_i">
                <div<? if ($subway && $subway->line): ?> style="background: <?= $subway->line->color ?>;"<? endif ?> class="shopLst_addrs">
                    <span class="shopLst_addrs_tx">
                        <? if ($subway): ?>
                            <?= $subway->name ?><br>
                        <? endif ?>
                        <span class="colorBrightGrey"><?= $point->address ?></span>
                    </span>

                    <span class="shopLst_addrs_tm"><?= $point->regtime ?></span>
                </div>
            </li>
        <? endforeach ?>
        </ul>

    </div>

    <div id="<?= $id . '-map' ?>" class="js-order-map selShop_r" data-value="<?= $helper->json($dataValue) ?>"></div>

</div>

<? };