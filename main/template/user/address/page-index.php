<?php
/**
 * @var $page                              \View\User\Address\IndexPage
 * @var $helper                            \Helper\TemplateHelper
 */
?>

<div class="personal">
    <?= $page->render('user/_menu-1508', ['page' => $page]) ?>

    <div class="personalPage">

        <div class="private-sections private-sections_gray grid">
            <h1 class="private-sections__head">Адреса доставки</h1>
            <div class="grid__col grid__col_2">
                    <ul class="address-list">
                        <li class=" address-list__item js-btnDelContainer js-copyContentFrom">
                                <div class="address-list__mode">Домашний</div>

                                <ul class="address-list-details">
                                    <li class="address-list-details__item">Мытищи</li>
                                    <li class="address-list-details__item">ул. Линии Октябрьской Железной Дороги
                                    </li>
                                    <li class="address-list-details__item">дом 16 корпус 2 квартира 245</li>
                                </ul>
                            <a class="address-list__item-del js-btnDelModal js-modalShow" href="#"></a>
                        </li>

                        <li class="address-list__item js-btnDelContainer js-copyContentFrom">
                                <ul class="address-list-details">
                                    <li class="address-list-details__item">Мытищи</li>
                                    <li class="address-list-details__item">ул. Линии Октябрьской Железной Дороги
                                    </li>
                                    <li class="address-list-details__item">дом 16 корпус 2 квартира 245</li>
                                </ul>
                            <a class="address-list__item-del js-btnDelModal js-modalShow" href="#"></a>
                        </li>
                    </ul>

            <!--#####Вывести если адреса отсутствуют

                <div class="item-none item-none_statis">
                    <div class="item-none__img-block">
                        <img src="#" alt="#">
                    </div>
                    <span class="item-none__txt">Чтобы не пришлось вводить адрес в следующий раз, добавь и сохрани его на этой странице. Можно добавить несколько адресов.</span>
                </div>-->
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
                        <form class="new-address__form" action="#" name="name">
                            <label class="new-address__form-item">
                                <input class="new-address__form-input" placeholder="Название" type="text">
                            </label>
                            <label class="new-address__form-item">
                                <input class="new-address__form-input" placeholder="Регион" type="text">
                            </label>
                            <label class="new-address__form-item">
                                <input class="new-address__form-input" placeholder="Улица" type="text">
                            </label>
                            <label class="new-address__form-item new-address__form-item_half">
                                <input class="new-address__form-input" placeholder="Дом" type="text">
                            </label>
                            <label class="new-address__form-item new-address__form-item_half">
                                <input class="new-address__form-input" placeholder="Квартира" type="text">
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
                    <div class="js-copyContentIn">

                    </div>
                    <button class="address-list__item-del_big js-btnContainerDel js-modal-close">Удалить</button>
                <a class="private-sections__modal-close js-modal-close" href="#"></a>
            </article>
        </div>

    </div>
</div>
