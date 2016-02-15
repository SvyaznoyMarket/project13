<?php return function(
    \Helper\TemplateHelper $helper,
    \Model\Region\Entity $region,
    $errors,
    $form
) { ?>
    <form class="js-userAddress-form new-address__form id-address-form" action="<?= $helper->url('user.address.create') ?>" name="name" method="post">
        <input type="hidden" name="address[kladrId]" data-field="kladrId" value="<?= !empty($form['kladrId']) ? $helper->escape($form['kladrId']) : $region->kladrId ?>">
        <input type="hidden" name="address[regionId]" data-field="regionId" value="<?= !empty($form['regionId']) ? $helper->escape($form['regionId']) : $region->id ?>">
        <input type="hidden" name="address[streetType]" data-field="streetType" value="<?= !empty($form['streetType']) ? $helper->escape($form['streetType']) : '' ?>">
        <input type="hidden" name="address[zipCode]" data-field="zipCode" value="<?= !empty($form['zipCode']) ? $helper->escape($form['zipCode']) : '' ?>">

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
                value="<?= !empty($form['regionName']) ? $helper->escape($form['regionName']) : $region->name ?>"
                >
        </label>

        <? if (!empty($errors['street']['message'])): ?>
            <span class="new-address__form-error">
                <?= $errors['street']['message'] ?>
            </span>
        <? endif ?>

        <label class="new-address__form-item">
            <input
                class="new-address__form-input js-user-address"
                name="address[street]"
                value="<?= $helper->escape($form['street']) ?>"
                placeholder="Улица"
                type="text"
                data-field="street"
                data-relation="<?= $helper->json(['form' => '.id-address-form'])?>"
                >
        </label>

        <? if (!empty($errors['building']['message'])): ?>
            <span class="new-address__form-error">
                <?= $errors['building']['message'] ?>
            </span>
        <? endif ?>

        <label class="new-address__form-item new-address__form-item_half">
            <input
                class="new-address__form-input new-address__form-input_half js-user-address"
                name="address[building]"
                value="<?= $helper->escape($form['building']) ?>"
                placeholder="Дом"
                type="text"
                data-field="building"
                data-relation="<?= $helper->json(['form' => '.id-address-form'])?>"
                >
        </label>
        <label class="new-address__form-item new-address__form-item_half new-address__form-item_half-odd">
            <input
                class="new-address__form-input"
                name="address[apartment]"
                value="<?= $helper->escape($form['apartment']) ?>"
                placeholder="Квартира"
                type="text"
                data-field="apartment"
                >
        </label>

        <input class="new-address__form-send" type="submit" value="Сохранить">
    </form>

<? };