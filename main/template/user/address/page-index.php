<?php
/**
 * @var $page      \View\User\Address\IndexPage
 * @var $helper    \Helper\TemplateHelper
 * @var $addresses \Model\User\Address\Entity[]
 */
?>

<div class="personal">
    <?= $page->render('user/_menu', ['page' => $page]) ?>

    <div class="personalPage">

        <div class="private-sections private-sections_gray grid">
            <h1 class="private-sections__head">Адреса доставки</h1>
            <div class="grid__col grid__col_2">
                <ul class="address-list">
                <? foreach ($addresses as $address): ?>
                    <li class=" address-list__item js-btnDelContainer js-copyContentFrom">
                        <? if ($address->description): ?><div class="address-list__mode"><?= $helper->escape($address->description) ?></div><? endif ?>

                        <ul class="address-list-details">
                            <? if ($address->region): ?><li class="address-list-details__item"><?= $helper->escape($address->region->name) ?></li><? endif ?>
                            <li class="address-list-details__item"><?= $helper->escape(implode(' ', [$address->streetType, $address->street])) ?></li>
                            <li class="address-list-details__item"><?= $helper->escape(implode(' ', [$address->building, $address->apartment])) ?></li>
                        </ul>
                        <a class="address-list__item-del js-btnDelModal js-modalShow" href="#"></a>
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
                    <form class="new-address__form" action="<?= $helper->url('user.address.create') ?>" name="name" method="post">
                        <input type="hidden" name="address[kladrId]" value="">

                        <label class="new-address__form-item">
                            <input class="new-address__form-input" name="address[description]" placeholder="Название" type="text">
                        </label>
                        <label class="new-address__form-item">
                            <input class="new-address__form-input" name="address[regionId]" placeholder="Регион" type="text">
                        </label>
                        <label class="new-address__form-item">
                            <input class="new-address__form-input" name="address[street]" placeholder="Улица" type="text">
                        </label>
                        <label class="new-address__form-item new-address__form-item_half">
                            <input class="new-address__form-input" name="address[building]" placeholder="Дом" type="text">
                        </label>
                        <label class="new-address__form-item new-address__form-item_half">
                            <input class="new-address__form-input" name="address[apartment]" placeholder="Квартира" type="text">
                        </label>

                        <input class="new-address__form-send" type="submit">
                    </form>
                </div>
            </div>

        </div>

        <div class="private-sections__modal js-modalLk">
            <article class="private-sections__modal-body private-sections__modal-body_small">
                <header class="private-sections__modal-head">
                    Удалить адрес?
                </header>
                <div class="js-copyContentIn"></div>
                <button class="address-list__item-del_big js-btnContainerDel js-modal-close">Удалить</button>
                <a class="private-sections__modal-close js-modal-close" href="#"></a>
            </article>
        </div>

    </div>
</div>
