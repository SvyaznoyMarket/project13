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

    <div class="private-sections grid js-ep-container">
        <div class="grid__col grid__col_2">
            <div class="grid__cell js-ep-item-top">
                <div
                    class="private-sections__item private-sections__item_order grid__inner js-private-sections-container">
                    <a href="#" class="private-sections__button  js-private-sections-button">
                        <span class="private-sections__button-icon js-private-sections-icon"></span>
                    </a>
                    <header class="private-sections__head js-ep-item-top-header">Текущие заказы</header>
                    <div class="grid-scroll js-private-sections-body">
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
                                    <div class="order-list__status-payment order-list__status-payment_warn">Требуется
                                        предоплата
                                    </div>
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
                                    <div class="order-list__status-payment order-list__status-payment_warn">Требуется
                                        предоплата
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>


            <div class="grid__cell js-ep-item-margin">
                <div class="private-sections__item private-sections grid__inner js-private-sections-container">
                    <a href="#" class="private-sections__button  js-private-sections-button">
                        <span class="private-sections__button-icon js-private-sections-icon"></span>
                    </a>

                    <header class="private-sections__head">Адреса</header>

                    <div class="grid-scroll js-private-sections-body">
                        <ul class="grid-scroll-list address-list">
                            <li class="grid-scroll-list__item address-list__item">
                                <a class="address-list__item-link" href="<?= $helper->url('user.address') ?>"
                                   target="_blank">
                                    <div class="address-list__mode">Домашний</div>

                                    <ul class="address-list-details">
                                        <li class="address-list-details__item">Мытищи</li>
                                        <li class="address-list-details__item">ул. Линии Октябрьской Железной Дороги
                                        </li>
                                        <li class="address-list-details__item">дом 16 корпус 2 квартира 245</li>
                                    </ul>
                                </a>

                            </li>

                            <li class="grid-scroll-list__item address-list__item">
                                <a class="address-list__item-link" href="<?= $helper->url('user.address') ?>"
                                   target="_blank">
                                    <ul class="address-list-details">
                                        <li class="address-list-details__item">Мытищи</li>
                                        <li class="address-list-details__item">ул. Линии Октябрьской Железной Дороги
                                        </li>
                                        <li class="address-list-details__item">дом 16 корпус 2 квартира 245</li>
                                    </ul>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>


            <div class="grid__cell">
                <div class="private-sections__item private-sections grid__inner js-private-sections-container">
                    <a href="#" class="private-sections__button  js-private-sections-button">
                        <span class="private-sections__button-icon js-private-sections-icon"></span>
                    </a>
                    <header class="private-sections__head">
                        <a class="message-list__link" href="<?= $helper->url('user.message') ?>" target="_blank">Сообщения</a>
                    </header>

                    <div class="grid-scroll js-private-sections-body">
                        <ul class="grid-scroll-list message-list">
                            <li class="grid-scroll-list__item message-list__item">
                                <a class="message-list__link clearfix" href="#" target="_blank">
                                    <div class="message-list__left">
                                        <div class="message-list__title message-list__title_new">
                                                Заказ #COXD-123456 накаав дылваолдыва
                                        </div>

                                        <p class="message-list__text">
                                            Ваш заказ передан в транспортную компанию! Ваш Enter! приятных гадин
                                        </p>
                                    </div>

                                    <div class="message-list__date">
                                        17.08.2015
                                    </div>
                                </a>
                            </li>

                            <li class="grid-scroll-list__item message-list__item">
                                <a class="message-list__link clearfix" href="#" target="_blank">
                                    <div class="message-list__left">
                                        <div class="message-list__title message-list__title_new">
                                            Заказ #COXD-123456 накаав дылваолдыва
                                        </div>

                                        <p class="message-list__text">
                                            Ваш заказ передан в транспортную компанию! Ваш Enter! приятных гадин
                                        </p>
                                    </div>

                                    <div class="message-list__date">
                                        17.08.2015
                                    </div>
                                </a>
                            </li>

                            <li class="grid-scroll-list__item message-list__item">
                                <a class="message-list__link clearfix" href="#" target="_blank">
                                    <div class="message-list__left">
                                        <div class="message-list__title">
                                            Заказ #COXD-123456 накаав дылваолдыва
                                        </div>

                                        <p class="message-list__text">
                                            Ваш заказ передан в транспортную компанию! Ваш Enter! приятных гадин
                                        </p>
                                    </div>

                                    <div class="message-list__date">
                                        17.08.2015
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid__col grid__col_2">
            <div class="grid__cell js-ep-pointReport">
                <div
                    class="private-sections__item private-sections__item_ep private-sections grid__inner js-private-sections-container">
                    <a href="#" class="private-sections__button  js-private-sections-button">
                        <span class="private-sections__button-icon js-private-sections-icon"></span>
                    </a>

                    <header class="private-sections__head">Горящие фишки</header>

                    <div class="grid-scroll js-private-sections-body">
                        <ul class="grid-scroll-list grid-scroll-list_2col private-ep-list ">
                            <li class="grid-scroll-list__col js-ep-item">
                                <div class="grid-scroll-list__item private-ep-list__item ">
                                <span class="ep-coupon"
                                      style="background-image: url(http://content.enter.ru/wp-content/uploads/2014/03/fishka_orange_b1.png);">
                                    <span class="ep-coupon__inner">
                                        <span class="ep-coupon__ico"><img
                                                src="http://content.enter.ru/wp-content/uploads/2014/03/bit.png"></span>

                                        <span class="ep-coupon__desc">Крупногабаритная и встраиваемая техника</span>

                                        <span class="ep-coupon__price">5%</span>
                                    </span>
                                </span>

                                    <div class="private-ep-list__dutation">3 д 19 ч 52 мин 51 с</div>
                                </div>
                            </li>

                            <li class="grid-scroll-list__col js-ep-item">
                                <div class="grid-scroll-list__item private-ep-list__item ">
                                <span class="ep-coupon"
                                      style="background-image: url(http://content.enter.ru/wp-content/uploads/2014/03/fishka_orange_b1.png);">
                                    <span class="ep-coupon__inner">
                                        <span class="ep-coupon__ico"><img
                                                src="http://content.enter.ru/wp-content/uploads/2014/03/kids.png"></span>

                                        <span class="ep-coupon__desc">Крупногабаритная и встраиваемая техника</span>

                                        <span class="ep-coupon__price">5%</span>
                                    </span>
                                </span>

                                    <div class="private-ep-list__dutation">3 д 19 ч 52 мин 51 с</div>
                                </div>
                            </li>

                            <!-- <li class="grid-scroll-list__col">
                                 <div class="grid-scroll-list__item private-ep-list__item">
                                 <span class="ep-coupon" style="background-image: url(http://content.enter.ru/wp-content/uploads/2014/03/fishka_lime_b1.png);">
                                     <span class="ep-coupon__inner">
                                         <span class="ep-coupon__ico"><img src="http://content.enter.ru/wp-content/uploads/2014/03/kids.png"></span>

                                         <span class="ep-coupon__desc">Гигиена и уход за малышом</span>

                                         <span class="ep-coupon__price">10%</span>
                                     </span>
                                 </span>

                                     <div class="private-ep-list__dutation">3 д 19 ч 52 мин 51 с</div>
                                 </div>
                             </li>-->
                        </ul>
                    </div>
                </div>
            </div>

            <div class="private-el-list__info clearfix">
                <div class="grid__cell grid__cell_2 private-ep-list__item-info ep-info js-ep-item-info">
                    <div class="ep-info__desc">
                        <h4 class="ep-info__desc-title">Фишка со скидкой 15% на Текстиль</h4>

                        <div class="ep-info__desc-duration">
                            с
                            <span class="ep-info__desc-duration-start">30.10.2016</span>
                            по
                            <span class="ep-info__desc-duration-end">30.20.2019</span>
                        </div>
                        <div class="ep-info__desc-timer">
                            <p class="ep-info__desc-timer-desc">До конца действия осталось</p>

                            <div class="ep-info__desc-timer-report">
                                <span>3 д</span>
                                <span>19 ч</span>
                                <span>52 мин</span>
                                <span>51 с</span>
                            </div>
                        </div>
                        <p class="ep-info__desc-txt">
                            Скидка по акции не суммируется
                            со скидками по другим акциям ООО "Энтер" Фишка не действует на товары продавцов-партнеров
                            Фишка действует на список товаров Минимальная сумма заказа 10 000 ₽
                        </p>
                    </div>

                    <div class="ep-info__product">
                        <div class="ep-info__row clearfix">
                            <h4 class="ep-info__product-title">
                                Действует на товары
                            </h4>

                            <a class="ep-info__product-all" href="#">
                                Посмотреть все
                            </a>
                        </div>
                        <div class="ep-info__product-slide js-epSlide">
                            <div class="ep-info__product-list-outer">
                                <ul class="ep-info__product-list clearfix js-epSlideList">

                                    <li class="ep-info__product-item js-epSlideItem">
                                        <div class="ep-info__product-img-block">
                                            <img src="#" alt="#">

                                            <p class="ep-info__product-img-title">Lorem ipsum dolor sit amet, consectetur
                                                adipisicing.</p>
                                        </div>
                                        <div class="ep-info__product-price">
                                            <div class="ep-info__product-price-old">
                                                <span>3809</span>
                                                <span class="rubl">p</span>
                                            </div>
                                            <div class="ep-info__product-price-new">
                                                <span>2809</span>
                                                <span class="rubl">p</span>
                                            </div>
                                        </div>
                                        <div class="ep-info__product-buy">
                                            Купить
                                        </div>
                                    </li>

                                    <li class="ep-info__product-item js-epSlideItem">
                                        <div class="ep-info__product-img-block">
                                            <img src="#" alt="#">

                                            <p class="ep-info__product-img-title">Lorem ipsum dolor sit amet, consectetur
                                                adipisicing.</p>
                                        </div>
                                        <div class="ep-info__product-price">
                                            <div class="ep-info__product-price-old">
                                                <span>3809</span>
                                                <span class="rubl">p</span>
                                            </div>
                                            <div class="ep-info__product-price-new">
                                                <span>2809</span>
                                                <span class="rubl">p</span>
                                            </div>
                                        </div>
                                        <div class="ep-info__product-buy">
                                            Купить
                                        </div>
                                    </li>

                                    <li class="ep-info__product-item js-epSlideItem">
                                        <div class="ep-info__product-img-block">
                                            <img src="#" alt="#">

                                            <p class="ep-info__product-img-title">Lorem ipsum dolor sit amet, consectetur
                                                adipisicing.</p>
                                        </div>
                                        <div class="ep-info__product-price">
                                            <div class="ep-info__product-price-old">
                                                <span>3809</span>
                                                <span class="rubl">p</span>
                                            </div>
                                            <div class="ep-info__product-price-new">
                                                <span>2809</span>
                                                <span class="rubl">p</span>
                                            </div>
                                        </div>
                                        <div class="ep-info__product-buy">
                                            Купить
                                        </div>
                                    </li>


                                    <li class="ep-info__product-item js-epSlideItem">
                                        <div class="ep-info__product-img-block">
                                            <img src="#" alt="#">

                                            <p class="ep-info__product-img-title">Lorem ipsum dolor sit amet, consectetur
                                                adipisicing.</p>
                                        </div>
                                        <div class="ep-info__product-price">
                                            <div class="ep-info__product-price-old">
                                                <span>3809</span>
                                                <span class="rubl">p</span>
                                            </div>
                                            <div class="ep-info__product-price-new">
                                                <span>2809</span>
                                                <span class="rubl">p</span>
                                            </div>
                                        </div>
                                        <div class="ep-info__product-buy">
                                            Купить
                                        </div>
                                    </li>

                                    <li class="ep-info__product-item js-epSlideItem">
                                        <div class="ep-info__product-img-block">
                                            <img src="#" alt="#">

                                            <p class="ep-info__product-img-title">Lorem ipsum dolor sit amet, consectetur
                                                adipisicing.</p>
                                        </div>
                                        <div class="ep-info__product-price">
                                            <div class="ep-info__product-price-old">
                                                <span>3809</span>
                                                <span class="rubl">p</span>
                                            </div>
                                            <div class="ep-info__product-price-new">
                                                <span>2809</span>
                                                <span class="rubl">p</span>
                                            </div>
                                        </div>
                                        <div class="ep-info__product-buy">
                                            Купить
                                        </div>
                                    </li>
                                </ul>
                            </div>


                            <div class="ep-info__product-controls js-epSlideControls">
                                <a class="ep-info__product-prev js-epSlideControlsPrev" href="#"></a>
                                <a class="ep-info__product-next js-epSlideControlsNext" href="#"></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid__cell grid__cell_2 private-ep-list__item-info js-ep-item-info">
                    2
                </div>
            </div>


            <div class="grid__cell">
                <div class="private-sections__item private-sections grid__inner js-private-sections-container">
                    <a href="#" class="private-sections__button  js-private-sections-button">
                        <span class="private-sections__button-icon js-private-sections-icon"></span>
                    </a>

                    <header class="private-sections__head">
                        <a class="private-sections__head-link" href="<?= $helper->url('user.favorites') ?>" target="_blank">Избранное</a>
                    </header>

                    <div class="grid-scroll js-private-sections-body">
                        <ul class="grid-scroll-list favorite-list">
                            <li class="grid-scroll-list__item favorite-list__item">
                                <a class="favorite-list__link" href="" target="_blank">
                                    <div class="favorite-list__views favorite-list__cell">
                                            <img
                                                src="http://8.imgenter.ru/uploads/sunny/08/b0/49/thumb_27dc_product_120.jpeg"
                                                class="image" alt="">
                                    </div>

                                    <div class="favorite-list__cell">
                                        <span>Самокат JD Bug Classic MS-305 синий</span>

                                        <div class="favorite-list__avail">В наличии</div>
                                    </div>

                                    <div class="favorite-list__price favorite-list__cell">
                                        5200 <span class="rubl">p</span>
                                    </div>
                                </a>
                            </li>

                            <li class="grid-scroll-list__item favorite-list__item">
                                <a class="favorite-list__link" href="" target="_blank">
                                    <div class="favorite-list__views favorite-list__cell">

                                            <img
                                                src="http://8.imgenter.ru/uploads/sunny/08/b0/49/thumb_27dc_product_120.jpeg"
                                                class="image" alt="">

                                    </div>

                                    <div class="favorite-list__cell">
                                        <span>Самокат JD Bug Classic MS-305 синий</span>

                                        <div class="favorite-list__avail favorite-list__avail_no">Нет в наличии</div>
                                    </div>

                                    <div class="favorite-list__price favorite-list__cell">
                                        5200 <span class="rubl">p</span>
                                    </div>
                                </a>
                            </li>

                            <li class="grid-scroll-list__item favorite-list__item">
                                <a class="favorite-list__link" href="">
                                    <div class="favorite-list__views favorite-list__cell">

                                            <img
                                                src="http://8.imgenter.ru/uploads/sunny/08/b0/49/thumb_27dc_product_120.jpeg"
                                                class="image" alt="">

                                    </div>

                                    <div class="favorite-list__cell">
                                        <span>Самокат JD Bug Classic MS-305 синий</span>

                                        <div class="favorite-list__avail">В наличии</div>
                                    </div>

                                    <div class="favorite-list__price favorite-list__cell">
                                        5200 <span class="rubl">p</span>
                                    </div>
                                </a>
                            </li>

                            <li class="grid-scroll-list__item favorite-list__item">
                                <a class="favorite-list__link" href="">
                                        <div class="favorite-list__views favorite-list__cell">

                                                <img
                                                    src="http://8.imgenter.ru/uploads/sunny/08/b0/49/thumb_27dc_product_120.jpeg"
                                                    class="image" alt="">

                                        </div>

                                        <div class="favorite-list__cell">
                                            <span>Самокат JD Bug Classic MS-305 синий</span>

                                            <div class="favorite-list__avail">В наличии</div>
                                        </div>

                                        <div class="favorite-list__price favorite-list__cell">
                                            5200 <span class="rubl">p</span>
                                        </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="grid__cell">
                <div class="private-sections__item private-sections grid__inner js-private-sections-container">
                    <a href="#" class="private-sections__button  js-private-sections-button">
                        <span class="private-sections__button-icon js-private-sections-icon"></span>
                    </a>

                    <header class="private-sections__head">
                        <a class="private-sections__head-link" href="<?= $helper->url('user.subscriptions') ?>">Подписки</a>
                    </header>

                    <div class="grid-scroll js-private-sections-body">
                        <ul class="grid-scroll-list subscribe-list">
                            <li class="grid-scroll-list__item subscribe-list__item">
                                <input class="customInput customInput-checkbox" type="checkbox" id="subscribe-ep"
                                       checked/>
                                <label class="customLabel" for="subscribe-ep">Новости EnterPrize</label>
                            </li>

                            <li class="grid-scroll-list__item subscribe-list__item">
                                <input class="customInput customInput-checkbox" type="checkbox" id="subscribe-ep-2"
                                       disabled/>
                                <label class="customLabel" for="subscribe-ep-2">Новости EnterPrize Новости EnterPrize
                                    Новости EnterPrize Новости EnterPrize Новости EnterPrize Новости EnterPrize</label>
                            </li>

                            <li class="grid-scroll-list__item subscribe-list__item">
                                <input class="customInput customInput-checkbox" type="checkbox" id="subscribe-ep-3"
                                       checked/>
                                <label class="customLabel" for="subscribe-ep-3">Новости EnterPrize</label>
                            </li>

                            <li class="grid-scroll-list__item subscribe-list__item">
                                <input class="customInput customInput-checkbox" type="checkbox" id="subscribe-ep-4"/>
                                <label class="customLabel" for="subscribe-ep-4">Новости EnterPrize</label>
                            </li>

                            <li class="grid-scroll-list__item subscribe-list__item">
                                <input class="customInput customInput-checkbox" type="checkbox" id="subscribe-ep-5"
                                       checked/>
                                <label class="customLabel" for="subscribe-ep-5">Новости EnterPrize</label>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <div class="viewed-slider js-viewed-slider-full">
        <div class="viewed-slider__row clearfix">
            <h3 class="viewed-slider__title">
                Вы смотрели
            </h3>

            <span class="viewed-slider__pagination">
                 Страница <span class="js-viewed-slider-page">1</span> из <span class="js-viewed-slider-allPage">1</span>
            </span>
        </div>
        <div class="viewed-slider__slide js-viewed-slider">
            <div class="viewed-slider__list-outer">
                <ul class="viewed-slider__list clearfix js-viewed-slider-list">

                    <li class="viewed-slider__item js-viewed-slider-item">
                        <div class="viewed-slider__img-block">
                            <img src="#" alt="#">

                            <p class="viewed-slider__img-title">Lorem ipsum dolor sit amet, consectetur
                                adipisicing.</p>
                        </div>
                        <div class="viewed-slider__price">
                            <div class="viewed-slider__price-old">
                                <span>3809</span>
                                <span class="rubl">p</span>
                            </div>
                            <div class="viewed-slider__price-new">
                                <span>2809</span>
                                <span class="rubl">p</span>
                            </div>
                        </div>
                        <div class="viewed-slider__buy">
                            Купить
                        </div>
                    </li>

                    <li class="viewed-slider__item js-viewed-slider-item">
                        <div class="viewed-slider__img-block">
                            <img src="#" alt="#">

                            <p class="viewed-slider__img-title">Lorem ipsum dolor sit amet, consectetur
                                adipisicing.</p>
                        </div>
                        <div class="viewed-slider__price">
                            <div class="viewed-slider__price-old">
                                <span>3809</span>
                                <span class="rubl">p</span>
                            </div>
                            <div class="viewed-slider__price-new">
                                <span>2809</span>
                                <span class="rubl">p</span>
                            </div>
                        </div>
                        <div class="viewed-slider__buy">
                            Купить
                        </div>
                    </li>

                    <li class="viewed-slider__item js-viewed-slider-item">
                        <div class="viewed-slider__img-block">
                            <img src="#" alt="#">

                            <p class="viewed-slider__img-title">Lorem ipsum dolor sit amet, consectetur
                                adipisicing.</p>
                        </div>
                        <div class="viewed-slider__price">
                            <div class="viewed-slider__price-old">
                                <span>3809</span>
                                <span class="rubl">p</span>
                            </div>
                            <div class="viewed-slider__price-new">
                                <span>2809</span>
                                <span class="rubl">p</span>
                            </div>
                        </div>
                        <div class="viewed-slider__buy">
                            Купить
                        </div>
                    </li>

                    <li class="viewed-slider__item js-viewed-slider-item">
                        <div class="viewed-slider__img-block">
                            <img src="#" alt="#">

                            <p class="viewed-slider__img-title">Lorem ipsum dolor sit amet, consectetur
                                adipisicing.</p>
                        </div>
                        <div class="viewed-slider__price">
                            <div class="viewed-slider__price-old">
                                <span>3809</span>
                                <span class="rubl">p</span>
                            </div>
                            <div class="viewed-slider__price-new">
                                <span>2809</span>
                                <span class="rubl">p</span>
                            </div>
                        </div>
                        <div class="viewed-slider__buy">
                            Купить
                        </div>
                    </li>

                    <li class="viewed-slider__item js-viewed-slider-item">
                        <div class="viewed-slider__img-block">
                            <img src="#" alt="#">

                            <p class="viewed-slider__img-title">Lorem ipsum dolor sit amet, consectetur
                                adipisicing.</p>
                        </div>
                        <div class="viewed-slider__price">
                            <div class="viewed-slider__price-old">
                                <span>3809</span>
                                <span class="rubl">p</span>
                            </div>
                            <div class="viewed-slider__price-new">
                                <span>2809</span>
                                <span class="rubl">p</span>
                            </div>
                        </div>
                        <div class="viewed-slider__buy">
                            Купить
                        </div>
                    </li>

                    <li class="viewed-slider__item js-viewed-slider-item">
                        <div class="viewed-slider__img-block">
                            <img src="#" alt="#">

                            <p class="viewed-slider__img-title">Lorem ipsum dolor sit amet, consectetur
                                adipisicing.</p>
                        </div>
                        <div class="viewed-slider__price">
                            <div class="viewed-slider__price-old">
                                <span>3809</span>
                                <span class="rubl">p</span>
                            </div>
                            <div class="viewed-slider__price-new">
                                <span>2809</span>
                                <span class="rubl">p</span>
                            </div>
                        </div>
                        <div class="viewed-slider__buy">
                            Купить
                        </div>
                    </li>

                    <li class="viewed-slider__item js-viewed-slider-item">
                        <div class="viewed-slider__img-block">
                            <img src="#" alt="#">

                            <p class="viewed-slider__img-title">Lorem ipsum dolor sit amet, consectetur
                                adipisicing.</p>
                        </div>
                        <div class="viewed-slider__price">
                            <div class="viewed-slider__price-old">
                                <span>3809</span>
                                <span class="rubl">p</span>
                            </div>
                            <div class="viewed-slider__price-new">
                                <span>2809</span>
                                <span class="rubl">p</span>
                            </div>
                        </div>
                        <div class="viewed-slider__buy">
                            Купить
                        </div>
                    </li>



                </ul>
            </div>


            <div class="viewed-slider__controls js-viewedSlideControls">
                <a class="viewed-slider__prev js-viewedSlideControlsPrev" href="#"></a>
                <a class="viewed-slider__next js-viewedSlideControlsNext" href="#"></a>
            </div>
        </div>
    </div>

    <?= $helper->render($isNewProductPage ? 'product-page/blocks/slider' : 'product/__slider', [
        'type' => 'main',
        'title' => 'Рекомендованное',
        'products' => $recommendedProducts,  //необходим $viewedProducts
        'limit' => \App::config()->product['itemsInSlider'],
        'page' => 1,
        'class' => $isNewProductPage ? '' : 'slideItem-7item',
        'sender' => [
            'name' => 'retailrocket',
            'position' => 'UserRecommended',
            'method' => 'PersonalRecommendation',
        ],
    ]) ?>

    <?= $helper->render('product-page/blocks/slider', [
        'type' => 'viewed',
        'title' => 'Вы смотрели',
        'products' => $viewedProducts,
        'limit' => \App::config()->product['itemsInSlider'],
        'page' => 1,
        'class' => 'slideItem-7item goods-slider_mark',
    ]) ?>

</div>