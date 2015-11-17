<?php
/**
 * @var $page      \View\User\Address\IndexPage
 * @var $helper    \Helper\TemplateHelper
 * @var $addresses \Model\User\Address\Entity[]
 */
?>

<?
$region = \App::user()->getRegion();
?>

<div id="kladr-config" data-value="<?= $helper->json(\App::config()->kladr ) ?>"></div>

<div class="personal">
    <?= $page->render('user/_menu', ['page' => $page]) ?>

    <div id="personal-container" class="personalPage">

        <div class="private-sections private-sections_gray grid">
            <h1 class="private-sections__head">Адреса доставки</h1>
            <div class="grid__col grid__col_2">
                <ul class="address-list">
                <? foreach ($addresses as $address): ?>
                    <li class=" address-list__item js-btnDelContainer js-copyContentFrom">
                        <? if ($address->description): ?><div class="address-list__mode"><?= $helper->escape($address->description) ?></div><? endif ?>

                        <ul class="address-list-details">
                            <? if ($address->region): ?><li class="address-list-details__item"><?= $helper->escape($address->region->name) ?></li><? endif ?>
                            <li class="address-list-details__item">
                                <? if ($address->street): ?><?= (($address->streetType && (false === strpos($address->street, $address->streetType . '.'))) ? ($address->streetType . '.') : '') ?><?= $address->street ?><? endif ?>
                            </li>
                            <li class="address-list-details__item">
                                <? if ($address->building): ?><?= (!empty($address->buildingType) ? $address->buildingType : 'д.') ?><?= $address->building ?><? endif ?>
                                <? if ($address->apartment): ?>кв.<?= $address->apartment ?><? endif ?>
                            </li>
                        </ul>
                        <a
                            class="address-list__item-del js-user-deleteAddress"
                            href="#"
                            data-value="<?= $helper->json([
                                'url'     => $helper->url('user.address.delete', ['addressId' => $address->id]),
                                'address' => $address,
                            ])?>"
                        ></a>
                    </li>
                <? endforeach ?>
                </ul>

                <? if (!$addresses): ?>
                <div class="item-none item-none_statis">
                    <div class="item-none__img-block">
                        <img src="/styles/personal-page/img/no-address.png" alt="#">
                    </div>
                    <span class="item-none__txt">Чтобы не пришлось вводить адрес в следующий раз, добавь и сохрани его на этой странице. Можно добавить несколько адресов.</span>
                </div>
                <? endif ?>
            </div>

            <div class="grid__col grid__col_2">
                <div class="new-address js-private-sections-container">
                    <div class="new-address__row">
                        <div class="new-address__title">Добавить новый адрес</div>
                        <a class="new-address__map-show js-mapShow js-private-sections-button" href="#">Показать карту</a>
                    </div>
                    <div class="new-address__map-block js-private-sections-body">
                        Вставить сюда карту
                    </div>
                    <? include __DIR__ . '/_form.php' ?>
                </div>
            </div>

        </div>

        <script id="tpl-user-deleteAddressPopup" type="text/html" data-partial="<?= $helper->json([]) ?>">
            <?= file_get_contents(\App::config()->templateDir . '/user/address/_deleteAddress-popup.mustache') ?>
        </script>

    </div>
</div>
