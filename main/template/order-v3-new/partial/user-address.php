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

    <?= $helper->render('order-v3-new/partial/delivery-interval', ['order' => $order, 'showTitle' => true]) ?>

    <h3 class="order-delivery__h3 order-delivery__h3_address">Адрес доставки:</h3>
    <? if ($userAddresses): ?>
        <div class="order-ctrl__custom-select order-ctrl__custom-select_address js-order-user-address-container">
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
    <? endif ?>

    <? $label = ($required ? '*' : '') . 'Улица'; ?>
    <div class="order-ctrl order-ctrl_input fullwidth">
        <label class="order-ctrl__txt js-order-ctrl__txt"><?= $helper->escape($label) ?></label>
        <span class="order-ctrl__address-error">Выберите адрес из списка</span>
        <input
            type="text"
            value="<?= $helper->escape($address['street']) ?>"
            class="order-ctrl__input js-order-ctrl__input js-order-deliveryAddress"
            data-field="street"
            <?= ($required ? 'required' : '') ?>
            data-value="<?= $helper->json($dataValue) ?>"
            data-text-default="<?= $helper->escape($label) ?>"
        />
    </div>

    <? $label = ($required ? '*' : '') . 'Дом'; ?>
    <div class="order-ctrl order-ctrl_input">
        <label class="order-ctrl__txt js-order-ctrl__txt"><?= $helper->escape($label) ?></label>
        <input
            type="text"
            value="<?= $helper->escape($address['building']) ?>"
            class="order-ctrl__input js-order-ctrl__input js-order-deliveryAddress"
            data-field="building"
            <?= ($required ? 'required' : '') ?>
            data-value="<?= $helper->json($dataValue) ?>"
            data-text-default="<?= $helper->escape($label) ?>"
        />
    </div>

    <? $label = 'Квартира'; ?>
    <div class="order-ctrl order-ctrl_input">
        <label class="order-ctrl__txt js-order-ctrl__txt"><?= $helper->escape($label) ?></label>
        <input
            type="text"
            value="<?= $helper->escape($address['apartment']) ?>"
            class="order-ctrl__input js-order-ctrl__input js-order-deliveryAddress"
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
