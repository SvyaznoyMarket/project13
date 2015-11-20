<?php
/**
 * @var $page      \View\User\Address\IndexPage
 * @var $helper    \Helper\TemplateHelper
 * @var $addresses \Model\User\Address\Entity[]
 * @var $region    \Model\Region\Entity
 */
?>

<form class="js-userAddress-form new-address__form id-address-form" action="<?= $helper->url('user.address.create') ?>" name="name" method="post">
    <input type="hidden" name="address[kladrId]" data-field="kladrId" value="<?= $region->kladrId ?>">
    <input type="hidden" name="address[regionId]" data-field="regionId" value="<?= $region->id ?>">
    <input type="hidden" name="address[streetType]" data-field="streetType" value="">
    <input type="hidden" name="address[zipCode]" data-field="zipCode" value="">

    <? if (false): ?>
    <label class="new-address__form-item">
        <input
            class="new-address__form-input"
            name="address[description]"
            placeholder="Название"
            type="text"
            data-field="description"
        >
    </label>
    <? endif ?>
    <label class="new-address__form-item">
        <input
            class="new-address__form-input js-user-address"
            name="address[regionName]"
            placeholder="Регион"
            type="text"
            data-url="<?= $helper->url('region.autocomplete') ?>"
            data-field="city"
            data-relation="<?= $helper->json(['form' => '.id-address-form'])?>"
            value="<?= $region->name ?>"
        >
    </label>
    <label class="new-address__form-item">
        <input
            class="new-address__form-input js-user-address"
            name="address[street]"
            placeholder="Улица"
            type="text"
            data-parent-kladr-id="<?= $region->kladrId ?>"
            data-field="street"
            data-parent-field="city"
            data-relation="<?= $helper->json(['form' => '.id-address-form'])?>"
        >
    </label>
    <label class="new-address__form-item new-address__form-item_half">
        <input
            class="new-address__form-input js-user-address"
            name="address[building]"
            placeholder="Дом"
            type="text"
            data-field="building"
            data-parent-field="street"
            data-relation="<?= $helper->json(['form' => '.id-address-form'])?>"
        >
    </label>
    <label class="new-address__form-item new-address__form-item_half">
        <input
            class="new-address__form-input"
            name="address[apartment]"
            placeholder="Квартира"
            type="text"
            data-field="apartment"
        >
    </label>

    <input class="new-address__form-send" type="submit">
</form>
