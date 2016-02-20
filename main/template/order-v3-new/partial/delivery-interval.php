<?php return function(
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity\Order $order
) {
    ?>

    <div class="order-delivery__info">
        <? /* дата доставки */ ?>
        <? if ($date = $order->delivery->date): ?>
            <? if ($order->delivery->dateInterval || $order->delivery->dayRange): ?>
                <div class="order-delivery__date" data-content="#id-order-changeDate-content-<?= $order->id ?>">
                    <? if ($order->delivery->dateInterval): ?>
                        <?= $helper->escape(sprintf('с %s по %s', (new \DateTime($order->delivery->dateInterval['from']))->format('d.m'), (new \DateTime($order->delivery->dateInterval['to']))->format('d.m'))) ?>
                    <? elseif (!empty($order->delivery->dayRange['name'])): ?>
                        <?= $helper->escape($order->delivery->dayRange['name']) ?>
                    <? elseif (!empty($order->delivery->dayRange['from']) && !empty($order->delivery->dayRange['to'])): ?>
                        <?= $helper->escape(sprintf('%s-%s %s', $order->delivery->dayRange['from'], $order->delivery->dayRange['to'], $helper->numberChoice($order->delivery->dayRange['to'], ['день', 'дня', 'дней']))) ?>
                    <? endif ?>
                </div>
            <? else: ?>
                <div class="order-delivery__date orderCol_date" data-content="#id-order-changeDate-content-<?= $order->id ?>"><?= $helper->escape(mb_strtolower(\Util\Date::strftimeRu('%e %B2 %Y', $date->format('U')))) ?></div>
            <? endif ?>
        <? endif ?>

        <?= $helper->render('order-v3-new/__calendar', [
            'id' => 'id-order-changeDate-content-' . $order->id,
            'possible_days' => $order->possible_days,
        ]) ?>

        <? /* время доставки */ ?>
        <? if ($order->possible_intervals): ?>
            <? if (count($order->possible_intervals) == 1 && $order->delivery->interval): ?>
                <div class="order-delivery__interval customSel one">
                    <span class="customSel_def"><?= $order->delivery->interval['from'] ?>-<?= $order->delivery->interval['to'] ?></span>
                </div>
            <? else: ?>
                <div class="order-ctrl__custom-select js-order-delivery-interval-dropbox-container">
                    <span class="order-ctrl__custom-select-item_title js-order-delivery-interval-dropbox-opener">
                        <? if ($order->delivery->interval): ?>
                            <?= $order->delivery->interval['from'] ?>-<?= $order->delivery->interval['to'] ?>
                        <? else: ?>
                            Время доставки
                        <? endif ?>
                    </span>

                    <ul class="order-ctrl__custom-select-list order-ctrl__custom-select-list_date js-order-delivery-interval-dropbox-content">
                        <? foreach ($order->possible_intervals as $interval): ?>
                            <li class="order-ctrl__custom-select-item js-order-delivery-interval-dropbox-item" data-value="<?= $helper->json($interval) ?>"><?= isset ($interval['from']) ? $interval['from'] : '' ?>-<?= isset ($interval['to']) ? $interval['to'] : '' ?></li>
                        <? endforeach ?>
                    </ul>
                </div>
            <? endif ?>
        <? endif ?>
    </div>

<? } ?>
 