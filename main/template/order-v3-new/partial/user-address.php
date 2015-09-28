<?php

$f = function(
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity\Order $order,
    \Model\OrderDelivery\Entity $orderDelivery
) {
    $address = [
        'street'    => null,
        'building'  => null,
        'number'    => null,
        'apartment' => null,
        'kladr_id'  => null,
    ];

    if ($orderDelivery->user_info && $orderDelivery->user_info->address) {
        $address = array_merge($address, $orderDelivery->user_info->address);
    }

    $dataValue = [
        'block_name' => $order->block_name,
    ];

    $containerId = 'id-order-deliveryAddress-' . ($order->block_name ?: uniqid());
?>
<div class="order-delivery__block deliv-addr jsSmartAddressBlock <?= $containerId ?>">
    <div class="order-ctrl fullwidth">
        <label class="order-ctrl__lbl js-order-ctrl__lbl ">Улица</label>
        <input
            type="text"
            value="<?= $address['street'] ?>"
            class="order-ctrl__input js-order-ctrl__input js-order-deliveryAddress"
            placeholder="Улица"
            data-field="street"
            data-value="<?= $helper->json($dataValue) ?>"
            data-relation="<?= $helper->json(['container' => '.' . $containerId])?>"
            data-parent-kladr-id="<?= \App::user()->getRegion()->kladrId ?>"
        />
    </div>
    <div class="order-ctrl">
        <label class="order-ctrl__lbl js-order-ctrl__lbl">Дом</label>
        <input
            type="text"
            value="<?= $address['building'] ?>"
            class="order-ctrl__input js-order-ctrl__input js-order-deliveryAddress"
            placeholder="Дом"
            data-field="building"
            data-value="<?= $helper->json($dataValue) ?>"
            data-relation="<?= $helper->json(['container' => '.' . $containerId])?>"
        />
    </div>
    <div class="order-ctrl">
        <label class="order-ctrl__lbl js-order-ctrl__lbl">Квартира</label>
        <input
            type="text"
            value="<?= $address['apartment'] ?>"
            class="order-ctrl__input js-order-ctrl__input js-order-deliveryAddress"
            placeholder="Квартира"
            data-field="apartment"
            data-value="<?= $helper->json($dataValue) ?>"
            data-relation="<?= $helper->json(['container' => '.' . $containerId])?>"
        />
    </div>
</div>
<? }; return $f;
 