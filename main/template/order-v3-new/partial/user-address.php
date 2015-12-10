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

    $required = (bool)$order->isPartnerOffer();
?>
<div class="order-delivery__block deliv-addr jsSmartAddressBlock <?= $containerId ?>">

    <!-- регион доставки -->
    <div class="order-region">Ваш регион: <span class="order-region__change jsChangeRegion"><?= \App::user()->getRegion()->getName() ?></span></div>
    <!--END регион доставки -->

    <div class="order-ctrl fullwidth">
        <div class="order-ctrl__custom-select">
            <span class="order-ctrl__custom-select-item_title">
                Выбрать адрес
            </span>

            <ul class="order-ctrl__custom-select-list">
                <li class="order-ctrl__custom-select-item">Lorem ipsum dolor sit amet.</li>
                <li class="order-ctrl__custom-select-item">Lorem ipsum dolor sit amet.</li>
                <li class="order-ctrl__custom-select-item">Lorem ipsum dolor sit amet.</li>
                <li class="order-ctrl__custom-select-item">Lorem ipsum dolor sit amet.</li>
                <li class="order-ctrl__custom-select-item">Lorem ipsum dolor sit amet.</li>
            </ul>
        </div>

    </div>

    <div class="order-ctrl fullwidth">
        <label class="order-ctrl__txt js-order-ctrl__txt"><?= ($required ? '*' : '') ?>Улица</label>
        <span class="order-ctrl__address-error">Выберите адрес из списка</span>
        <input
            type="text"
            value="<?= $address['street'] ?>"
            class="order-ctrl__input order-ctrl__input_float-label error js-order-ctrl__input js-order-deliveryAddress"
            data-field="street"
            <?= ($required ? 'required' : '') ?>
            data-value="<?= $helper->json($dataValue) ?>"
            data-relation="<?= $helper->json(['container' => '.' . $containerId])?>"
            data-text-default="Улица"
            data-parent-kladr-id="<?= \App::user()->getRegion()->kladrId ?>"
        />
    </div>
    <div class="order-ctrl">
        <label class="order-ctrl__txt js-order-ctrl__txt"><?= ($required ? '*' : '') ?>Дом</label>
        <input
            type="text"
            value="<?= $address['building'] ?>"
            class="order-ctrl__input order-ctrl__input_float-label error js-order-ctrl__input js-order-deliveryAddress"
            data-field="building"
            <?= ($required ? 'required' : '') ?>
            data-value="<?= $helper->json($dataValue) ?>"
            data-text-default="Дом"
            data-relation="<?= $helper->json(['container' => '.' . $containerId])?>"
        />
    </div>
    <div class="order-ctrl">
        <label class="order-ctrl__txt js-order-ctrl__txt">Квартира</label>
        <input
            type="text"
            value="<?= $address['apartment'] ?>"
            class="order-ctrl__input order-ctrl__input_float-label js-order-ctrl__input js-order-deliveryAddress"
            data-field="apartment"
            data-value="<?= $helper->json($dataValue) ?>"
            data-relation="<?= $helper->json(['container' => '.' . $containerId])?>"
        />
    </div>

    <div class="order-ctrl order-ctrl_last">
        <input class="customInput customInput-checkbox" type="checkbox" id="orderCtrlCheck">
        <label class="customLabel order-ctrl__check" for="orderCtrlCheck">Сохранить адрес</label>
    </div>
</div>
<? }; return $f;
