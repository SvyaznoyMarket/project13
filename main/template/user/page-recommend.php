<?php
/**
 * @var $page                \View\User\OrderPage
 * @var $user                \Session\User
 * @var $recommendedProducts \Model\Product\Entity[]
 * @var $viewedProducts      \Model\Product\Entity[]
 */
?>

<?
$isOldView = \App::abTest()->isOldPrivate();

$helper = new \Helper\TemplateHelper();
$isNewProductPage = \App::abTest()->isNewProductPage();
?>

<div class="personalPage personal">

    <?= $page->render($isOldView ? 'user/_menu' : 'user/_menu-1508', ['page' => $page]) ?>

    <div class="private-sections grid grid_2col">
        <div class="grid__cell">
            <div class="private-sections__item private-sections__item_order grid__inner">
                <header class="private-sections__head">Текущие заказы</header>
                <div class="grid-scroll">
                    <ul class="grid-scroll-list order-list">
                        <li class="grid-scroll-list__item order-list__item">
                            <div class="order-list__data">
                                <a class="order-list__data-number" href="">COXF-767608</a>
                                <div class="order-list__data-date">17.06.2015</div>
                            </div>

                            <div class="order-list__price">
                                60 740 <span class="rubl">p</span>
                            </div>

                            <div class="order-list__status">
                                <div class="order-list__status-confrm">Создан</div>
                                <div class="order-list__status-payment order-list__status-payment_warn">Требуется предоплата</div>
                            </div>
                        </li>

                        <li class="grid-scroll-list__item order-list__item">
                            <div class="order-list__data">
                                <a class="order-list__data-number" href="">COXF-767608</a>
                                <div class="order-list__data-date">17.06.2015</div>
                            </div>

                            <div class="order-list__price">
                                60 740 <span class="rubl">p</span>
                            </div>

                            <div class="order-list__status">
                                <div class="order-list__status-confrm">Создан</div>
                                <div class="order-list__status-payment">Оплачен</div>
                            </div>
                        </li>

                        <li class="grid-scroll-list__item order-list__item">
                            <div class="order-list__data">
                                <a class="order-list__data-number" href="">COXF-767608</a>
                                <div class="order-list__data-date">17.06.2015</div>
                            </div>

                            <div class="order-list__price">
                                60 740 <span class="rubl">p</span>
                            </div>

                            <div class="order-list__status">
                                <div class="order-list__status-confrm">Создан</div>
                                <div class="order-list__status-payment order-list__status-payment_warn">Требуется предоплата</div>
                            </div>
                        </li>

                        <li class="grid-scroll-list__item order-list__item">
                            <div class="order-list__data">
                                <a class="order-list__data-number" href="">COXF-767608</a>
                                <div class="order-list__data-date">17.06.2015</div>
                            </div>

                            <div class="order-list__price">
                                60 740 <span class="rubl">p</span>
                            </div>

                            <div class="order-list__status">
                                <div class="order-list__status-confrm">Создан</div>
                                <div class="order-list__status-payment order-list__status-payment_warn">Требуется предоплата</div>
                            </div>
                        </li>

                        <li class="grid-scroll-list__item order-list__item">
                            <div class="order-list__data">
                                <a class="order-list__data-number" href="">COXF-767608</a>
                                <div class="order-list__data-date">17.06.2015</div>
                            </div>

                            <div class="order-list__price">
                                60 740 <span class="rubl">p</span>
                            </div>

                            <div class="order-list__status">
                                <div class="order-list__status-confrm">Создан</div>
                                <div class="order-list__status-payment order-list__status-payment_warn">Требуется предоплата</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="grid__cell">
            <div class="private-sections__item private-sections__item_ep private-sections grid__inner">
                <header class="private-sections__head">Горящие фишки</header>


            </div>
        </div>

        <div class="grid__cell">
            <div class="private-sections__item private-sections grid__inner">
                <header class="private-sections__head">Адреса</header>

                <div class="grid-scroll">
                    <ul class="grid-scroll-list address-list">
                        <li class="grid-scroll-list__item address-list__item">
                            <div class="address-list__mode">Домашний</div>

                            <ul class="address-list-details">
                                <li class="address-list-details__item">Мытищи</li>
                                <li class="address-list-details__item">ул. Линии Октябрьской Железной Дороги</li>
                                <li class="address-list-details__item">дом 16 корпус 2 квартира 245</li>
                            </ul>
                        </li>

                        <li class="grid-scroll-list__item address-list__item">
                            <ul class="address-list-details">
                                <li class="address-list-details__item">Мытищи</li>
                                <li class="address-list-details__item">ул. Линии Октябрьской Железной Дороги</li>
                                <li class="address-list-details__item">дом 16 корпус 2 квартира 245</li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="grid__cell">
            <div class="private-sections__item private-sections grid__inner">
                <header class="private-sections__head">Избранное</header>

                <div class="grid-scroll">
                    <ul class="grid-scroll-list favorite-list">
                        <li class="grid-scroll-list__item favorite-list__item">
                            <div class="favorite-list__views favorite-list__cell">
                                <a class="favorite-list__image" href="">
                                    <img src="http://8.imgenter.ru/uploads/sunny/08/b0/49/thumb_27dc_product_120.jpeg" class="image" alt="">
                                </a>
                            </div>

                            <div class="favorite-list__cell">
                                <a class="favorite-list__name" href="">Самокат JD Bug Classic MS-305 синий</a>

                                <div class="favorite-list__avail">В наличии</div>
                            </div>

                            <div class="favorite-list__price favorite-list__cell">
                                5200 <span class="rubl">p</span>
                            </div>
                        </li>

                        <li class="grid-scroll-list__item favorite-list__item">
                            <div class="favorite-list__views favorite-list__cell">
                                <a class="favorite-list__image" href="">
                                    <img src="http://8.imgenter.ru/uploads/sunny/08/b0/49/thumb_27dc_product_120.jpeg" class="image" alt="">
                                </a>
                            </div>

                            <div class="favorite-list__cell">
                                <a class="favorite-list__name" href="">Самокат JD Bug Classic MS-305 синий</a>

                                <div class="favorite-list__avail favorite-list__avail_no">Нет в наличии</div>
                            </div>

                            <div class="favorite-list__price favorite-list__cell">
                                5200 <span class="rubl">p</span>
                            </div>
                        </li>

                        <li class="grid-scroll-list__item favorite-list__item">
                            <div class="favorite-list__views favorite-list__cell">
                                <a class="favorite-list__image" href="">
                                    <img src="http://8.imgenter.ru/uploads/sunny/08/b0/49/thumb_27dc_product_120.jpeg" class="image" alt="">
                                </a>
                            </div>

                            <div class="favorite-list__cell">
                                <a class="favorite-list__name" href="">Самокат JD Bug Classic MS-305 синий</a>

                                <div class="favorite-list__avail">В наличии</div>
                            </div>

                            <div class="favorite-list__price favorite-list__cell">
                                5200 <span class="rubl">p</span>
                            </div>
                        </li>

                        <li class="grid-scroll-list__item favorite-list__item">
                            <div class="favorite-list__views favorite-list__cell">
                                <a class="favorite-list__image" href="">
                                    <img src="http://8.imgenter.ru/uploads/sunny/08/b0/49/thumb_27dc_product_120.jpeg" class="image" alt="">
                                </a>
                            </div>

                            <div class="favorite-list__cell">
                                <a class="favorite-list__name" href="">Самокат JD Bug Classic MS-305 синий</a>

                                <div class="favorite-list__avail">В наличии</div>
                            </div>

                            <div class="favorite-list__price favorite-list__cell">
                                5200 <span class="rubl">p</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="grid__cell">
            <div class="private-sections__item private-sections grid__inner">
                <header class="private-sections__head">Сообщения</header>

                <div class="grid-scroll">
                    <ul class="grid-scroll-list message-list">
                        <li class="grid-scroll-list__item message-list__item">
                            <div class="message-list__left">
                                <div class="message-list__title">Заказ #COXD-123456 накаав дылваолдыва</div>

                                <p class="message-list__text">
                                    Ваш заказ передан в транспортную компанию! Ваш Enter! приятных гадин
                                </p>
                            </div>

                            <div class="message-list__date">
                                17.08.2015
                            </div>
                        </li>

                        <li class="grid-scroll-list__item message-list__item">
                            <div class="message-list__left">
                                <div class="message-list__title">Заказ #COXD-123456 накаав</div>

                                <p class="message-list__text">
                                    Ваш заказ передан в транспортную компанию!
                                </p>
                            </div>

                            <div class="message-list__date">
                                17.08.2015
                            </div>
                        </li>

                        <li class="grid-scroll-list__item message-list__item">
                            <div class="message-list__left">
                                <div class="message-list__title">Заказ #COXD-123456 накаав дылваолдыва</div>

                                <p class="message-list__text">
                                    Ваш заказ передан в транспортную компанию! Ваш Enter! приятных гадин
                                </p>
                            </div>

                            <div class="message-list__date">
                                17.08.2015
                            </div>
                        </li>

                        <li class="grid-scroll-list__item message-list__item">
                            <div class="message-list__left">
                                <div class="message-list__title">Заказ #COXD-123456 накаав дылваолдыва</div>

                                <p class="message-list__text">
                                    Ваш заказ передан в транспортную компанию! Ваш Enter! приятных гадин
                                </p>
                            </div>

                            <div class="message-list__date">
                                17.08.2015
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="grid__cell">
            <div class="private-sections__item private-sections grid__inner">
                <header class="private-sections__head">Подписки</header>

                <div class="grid-scroll">
                    <ul class="grid-scroll-list address-list">
                        <li class="grid-scroll-list__item address-list__item">
                            <input class="customInput customInput-checkbox" type="checkbox" id="subscribe-ep" />
                            <label class="customLabel" for="subscribe-ep">Новости EnterPrize</label>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


    <?= $helper->render($isNewProductPage ? 'product-page/blocks/slider' : 'product/__slider', [
        'type'           => 'main',
        'title'          => 'Мы рекомендуем',
        'products'       => $recommendedProducts,
        'limit'          => \App::config()->product['itemsInSlider'],
        'page'           => 1,
        'class'          => $isNewProductPage ? '' : 'slideItem-7item',
        'sender'   => [
            'name'     => 'retailrocket',
            'position' => 'UserRecommended',
            'method'   => 'PersonalRecommendation',
        ],
    ]) ?>

    <?= $helper->render('product/__slider', [
        'type'      => 'viewed',
        'title'     => 'Вы смотрели',
        'products'  => $viewedProducts,
        'limit'     => \App::config()->product['itemsInSlider'],
        'page'      => 1,
        'class'     => 'slideItem-7item',
    ]) ?>

</div>