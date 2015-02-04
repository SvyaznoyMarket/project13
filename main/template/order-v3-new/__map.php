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

    <div class="selShop_h">
        <? foreach ($order->possible_points as $token => $points) : ?>
        <div class="selShop_tab" data-token="<?= $token ?>"><?= $orderDelivery->points[$token]->block_name ?></div>
        <? endforeach ?>
    </div>

    <? foreach ($order->possible_points as $token => $points) : ?>

    <div class="selShop_l" data-token="<?= $token ?>">
        <ul class="shopLst">
        <? foreach ($points as $point): ?>
            <?
                $p = $point['point'];
                $subway = (isset($p->subway) && isset($p->subway[0])) ? $p->subway[0] : null;
                $nearestDay = '';
                try { $nearestDay = !empty($point['nearestDay']) ? $helper->humanizeDate(new \DateTime($point['nearestDay'])) : ''; } catch(\Exception $e) {}
            ?>

            <li class="shopLst_i jsChangePoint" data-id="<?= $p->id ?>" data-token="<?= $token ?>">
                <div class="shopLst_addrs">
                    <? if ($subway): ?>
                        <div class="shopLst_metro" <? if ($subway && $subway->line): ?> style="background: <?= $subway->line->color ?>;"<? endif ?>>
                            <div class="shopLst_metro_inn">м. <?= $subway->name ?></div>
                        </div>
                    <? endif ?>

                    <? if ($nearestDay): ?>
                        <div class="shopLst_stick"><?= $nearestDay ?></div>
                    <? endif ?>

                    <div class="shopLst_ln"><?= $p->address ?></div>
                    <div class="shopLst_ln"><?= $p->regtime ?></div>
                </div>
            </li>
        <? endforeach ?>
        </ul>

    </div>

    <? endforeach; ?>

    <div id="<?= $id . '-map' ?>" class="js-order-map selShop_r" data-value="<?= $helper->json($dataValue) ?>"></div>

</div>

<? };