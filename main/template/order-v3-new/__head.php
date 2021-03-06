<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param $step
 * @return string
 */
$f = function(
    \Helper\TemplateHelper $helper,
    $step
) {
    if (!in_array($step, [1, 2, 3])) {
        return '';
    }

    $links = [
        1 => [
            'name'     => 'Получатель',
            'url'      => $helper->url('orderV3'),
            'isActive' => false,
            'isPassed' => false,
        ],
        2 => [
            'name'     => 'Самовывоз и доставка',
            'url'      => $helper->url('orderV3.delivery'),
            'isActive' => false,
            'isPassed' => false,
        ],
        3 => [
            'name'     => 'Оплата',
            'url'      => $helper->url('orderV3.complete'),
            'isActive' => false,
            'isPassed' => false,
        ],
    ];

    $link = null;
    foreach ($links as $iStep => &$link) {
        $link['isActive'] = $iStep == $step;
        $link['isPassed'] = ($step < 3) && ($iStep < $step);
    }
    unset($link);
?>

<div style="display: none" class="jsRegion" data-value="<?= \App::user()->getRegion() ? \App::user()->getRegion()->getName() : '' ?>"></div>

<!-- шапка оформления заказа -->
<header class="orderHd orderHd-v2">
    <div class="order-head__inn">
        <div class="orderHd_l">
            <img class="orderHd_lg" src="/styles/order/img/logo.png" />
            <div class="orderHd_t">Оформление заказа</div>
        </div>

        <!-- если шаг пройден то orderHd_stps_i-pass, текущий шаг orderHd_stps_i-act -->
        <ul class="orderHd_stps">
        <? foreach ($links as $step => $link): ?>
            <li class="orderHd_stps_i<? if ($link['isPassed']): ?> orderHd_stps_i-pass<? endif ?><? if ($link['isActive']): ?> orderHd_stps_i-act<? endif ?>">
                <? if ($link['isPassed']): ?>
                    <a href="<?= $link['url'] ?>"><?= $link['name'] ?></a>
                <? else: ?>
                    <?= $link['name'] ?>
                <? endif ?>
            </li>
        <? endforeach ?>
        </ul>
    </div>
</header>
<!--/ шапка оформления заказа -->

<? }; return $f;