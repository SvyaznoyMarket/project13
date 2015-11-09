<?php return function(
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity\Order $order
) {

    // доступен только один интервал доставки ?
    $isOneInterval = count($order->possible_intervals) == 1;

    ?>

    <div class="order-delivery__interval customSel <?= $isOneInterval ? 'one' : 'jsShowDeliveryIntervals' ?>">
        <? if ($order->delivery->interval) : ?>
            <span class="customSel_def"><?= $order->delivery->interval['from'] ?>…<?= $order->delivery->interval['to'] ?></span>
        <? else : ?>
            <span class="customSel_def">Время доставки</span>
        <? endif ?>

        <ul class="customSel_lst popupFl" style="display: none;">
            <? foreach ($order->possible_intervals as $interval) : ?>
                <li class="customSel_i" data-value="<?= $helper->json($interval) ?>"><?= isset ($interval['from']) ? $interval['from'] : '' ?>…<?= isset ($interval['to']) ? $interval['to'] : '' ?></li>
            <? endforeach; ?>
        </ul>
    </div>

<? } ?>
 