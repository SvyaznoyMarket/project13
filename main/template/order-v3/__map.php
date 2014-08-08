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

    /** @var \Model\OrderDelivery\Entity\Point\Shop[]|\Model\OrderDelivery\Entity\Point\Pickpoint[] $points */
    foreach ($order->possible_points as $token => $points) {
        foreach ($points as $point) {
            $dataValue['points'][$token][] = [
                'id' => $point->id,
                'name' => $point->name,
                'address' => $point->address,
                'latitude' => $point->latitude,
                'longitude' => $point->longitude,
                'marker'    => $orderDelivery->points[$token]->marker
            ];
        }
    }
?>

<div id="<?= $id ?>" class="selShop popupFl" style="display: none;">
    <div class="js-order-changePlace-close popupFl_clsr" data-content="#<?= $id ?>"></div>

    <div class="selShop_h">
        <? foreach ($order->possible_points as $token => $points) : ?>
        <div class="selShop_tab" data-token="<?= $token ?>"><?= $orderDelivery->points[$token]->block_name ?></div>
        <? endforeach ?>
    </div>

    <? foreach ($order->possible_points as $token => $points) : ?>

    <div class="selShop_l" data-token="<?= $token ?>">
        <ul class="shopLst">
        <? foreach ($points as $point): ?>
            <? $subway = (isset($point->subway) && isset($point->subway[0])) ? $point->subway[0] : null ?>

            <li class="shopLst_i" data-id="<?= $point->id ?>" data-token="<?= $token ?>">
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

    <? endforeach; ?>

    <div id="<?= $id . '-map' ?>" class="js-order-map selShop_r" data-value="<?= $helper->json($dataValue) ?>"></div>

</div>

<? };