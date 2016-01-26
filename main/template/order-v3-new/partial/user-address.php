<?php

/**
 * @param \Model\User\Address\Entity[] $userAddresses
 * @param \Model\OrderDelivery\UserInfoAddressAddition $userInfoAddressAddition
 */
$f = function(
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity\Order $order,
    \Model\OrderDelivery\Entity $orderDelivery,
    array $userAddresses = [],
    \Model\OrderDelivery\UserInfoAddressAddition $userInfoAddressAddition = null
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

    if (empty($address['kladr_id'])) {
        $address['kladr_id'] = \App::user()->getRegion()->kladrId;
    }

    $dataValue = [
        'block_name' => $order->block_name,
    ];

    $required = $order->isPartnerOffer();
?>
<div
    class="order-delivery__block deliv-addr jsSmartAddressBlock"
    data-kladr-id="<?= $helper->escape($address['kladr_id']) ?>"
    data-kladr-zip-code="<?= $helper->escape($userInfoAddressAddition->kladrZipCode) ?>"
    data-kladr-street="<?= $helper->escape($userInfoAddressAddition->kladrStreet) ?>"
    data-kladr-street-type="<?= $helper->escape($userInfoAddressAddition->kladrStreetType) ?>"
    data-kladr-building="<?= $helper->escape($userInfoAddressAddition->kladrBuilding) ?>"
>

    <!-- регион доставки -->
    <div class="order-region">Ваш регион: <span class="order-region__change jsChangeRegion"><?= \App::user()->getRegion()->getName() ?></span></div>
    <!--END регион доставки -->

    <? if ($userAddresses): ?>
        <div class="order-ctrl fullwidth">
            <div class="order-ctrl__custom-select js-order-user-address-container">
                <span class="order-ctrl__custom-select-item_title js-order-user-address-opener">
                    Выбрать адрес
                </span>

                <ul class="order-ctrl__custom-select-list js-order-user-address-content">
                    <? foreach ($userAddresses as $userAddress): ?>
                        <li class="order-ctrl__custom-select-item js-order-user-address-item"
                            data-kladr-id="<?= $helper->escape($userAddress->kladrId) ?>"
                            data-zip-code="<?= $helper->escape($userAddress->zipCode) ?>"
                            data-street="<?= $helper->escape($userAddress->street) ?>"
                            data-street-type="<?= $helper->escape($userAddress->streetType) ?>"
                            data-building="<?= $helper->escape($userAddress->building) ?>"
                            data-apartment="<?= $helper->escape($userAddress->apartment) ?>"
                        ><?= $helper->escape($userAddress->address) ?></li>
                    <? endforeach ?>
                </ul>
            </div>
        </div>
    <? endif ?>

    <div class="order-ctrl fullwidth">
        <label class="order-ctrl__txt js-order-ctrl__txt"><?= ($required ? '*' : '') ?>Улица</label>
        <span class="order-ctrl__address-error">Выберите адрес из списка</span>
        <input
            type="text"
            value="<?= $helper->escape($address['street']) ?>"
            class="order-ctrl__input order-ctrl__input_float-label js-order-ctrl__input js-order-deliveryAddress"
            data-field="street"
            <?= ($required ? 'required' : '') ?>
            data-value="<?= $helper->json($dataValue) ?>"
            data-text-default="Улица"
        />
    </div>
    <div class="order-ctrl">
        <label class="order-ctrl__txt js-order-ctrl__txt"><?= ($required ? '*' : '') ?>Дом</label>
        <input
            type="text"
            value="<?= $helper->escape($address['building']) ?>"
            class="order-ctrl__input order-ctrl__input_float-label js-order-ctrl__input js-order-deliveryAddress"
            data-field="building"
            <?= ($required ? 'required' : '') ?>
            data-value="<?= $helper->json($dataValue) ?>"
            data-text-default="Дом"
        />
    </div>
    <div class="order-ctrl">
        <label class="order-ctrl__txt js-order-ctrl__txt">Квартира</label>
        <input
            type="text"
            value="<?= $helper->escape($address['apartment']) ?>"
            class="order-ctrl__input order-ctrl__input_float-label js-order-ctrl__input js-order-deliveryAddress"
            data-field="apartment"
            data-value="<?= $helper->json($dataValue) ?>"
        />
    </div>

    <? if (\App::user()->getEntity()): ?>
        <div class="order-ctrl order-ctrl_last">
            <input type="checkbox" id="orderCtrlCheck-<?= $helper->escape($order->block_name) ?>" class="customInput customInput-checkbox js-order-saveAddress" <? if ($userInfoAddressAddition->isSaveAddressChecked): ?>checked="checked"<? endif ?> <? if ($userInfoAddressAddition->isSaveAddressDisabled): ?>disabled="disabled"<? endif ?>>
            <label class="customLabel order-ctrl__check" for="orderCtrlCheck-<?= $helper->escape($order->block_name) ?>">Сохранить адрес</label>
        </div>
    <? endif ?>
</div>
<? }; return $f;
