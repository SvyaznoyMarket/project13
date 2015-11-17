<?php
/**
 * @var $page                \View\User\OrderPage
 * @var $user                \Session\User
 * @var $orders              \Model\Order\Entity[]
 * @var $coupons             \Model\EnterprizeCoupon\Entity[]
 * @var $addresses           \Model\User\Address\Entity[]
 * @var $recommendedProducts \Model\Product\Entity[]
 * @var $viewedProducts      \Model\Product\Entity[]
 */
?>

<?
$helper = new \Helper\TemplateHelper();
?>

<div class="personalPage personal">

    <?= $page->render('user/_menu', ['page' => $page]) ?>

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
                        <? foreach ($orders as $order): ?>
                            <li class="grid-scroll-list__item order-list__item">
                                <div class="order-list__data">
                                    <a class="order-list__data-number" href="<?= $helper->url('user.order', ['orderId' => $order->id]) ?>"><?= $order->numberErp ?></a>

                                    <div class="order-list__data-date"><?= $order->createdAt ? $order->createdAt->format('d.m.Y') : '' ?></div>
                                </div>

                                <div class="order-list__price">
                                <? if ($order->paySum): ?>
                                    <?= $helper->formatPrice($order->paySum) ?> <span class="rubl">p</span>
                                <? endif ?>
                                </div>

                                <div class="order-list__status">
                                    <div class="order-list__status-confrm">Создан</div>
                                    <? if ($order->prepaidSum): ?>
                                    <div class="order-list__status-payment order-list__status-payment_warn">Требуется предоплата</div>
                                    <? elseif ($status = $order->status): ?>
                                        <div class="order-list__status-payment"><?= $status->name ?></div>
                                    <? endif ?>
                                </div>
                            </li>
                        <? endforeach ?>
                        </ul>

                        <? if (!$orders): ?>
                        <div class="item-none">
                            <span class="item-none__txt">У вас еще нет заказов</span>
                            <a class="item-none__link" href="/">Перейти к покупкам</a>
                        </div>
                        <? endif ?>
                    </div>
                </div>
            </div>


            <div class="grid__cell js-ep-item-margin">
                <div class="private-sections__item grid__inner js-private-sections-container">
                    <a href="#" class="private-sections__button  js-private-sections-button">
                        <span class="private-sections__button-icon js-private-sections-icon"></span>
                    </a>

                    <header class="private-sections__head">Адреса</header>

                    <div class="grid-scroll js-private-sections-body">
                        <ul class="grid-scroll-list address-list">
                        <? foreach ($addresses as $address): ?>
                            <li class="grid-scroll-list__item address-list__item">
                                <a class="address-list__item-link" href="" target="_blank">
                                    <? if ($address->description): ?><div class="address-list__mode"><?= $helper->escape($address->description) ?></div><? endif ?>

                                    <ul class="address-list-details">
                                        <? if ($address->region): ?><li class="address-list-details__item"><?= $address->region->name ?></li><? endif ?>
                                        <li class="address-list-details__item">
                                            <? if ($address->street): ?><?= (($address->streetType && (false === strpos($address->street, $address->streetType . '.'))) ? ($address->streetType . '.') : '') ?><?= $address->street ?><? endif ?>
                                        </li>
                                        <li class="address-list-details__item">
                                            <? if ($address->building): ?><?= (!empty($address->buildingType) ? $address->buildingType : 'д.') ?><?= $address->building ?><? endif ?>
                                            <? if ($address->apartment): ?>кв.<?= $address->apartment ?><? endif ?>
                                        </li>
                                    </ul>
                                </a>
                            </li>
                        <? endforeach ?>
                        </ul>

                        <? if (false): ?>
                        <div class="item-none">
                            <span class="item-none__txt">Мы пока не знаем куда доставить ваши товары</span>
                            <a class="item-none__link" href="#">Добавить адрес доставки</a>
                        </div>
                        <? endif ?>
                    </div>
                </div>
            </div>


            <div class="grid__cell">
                <div class="private-sections__item grid__inner js-private-sections-container">
                    <a href="#" class="private-sections__button  js-private-sections-button">
                        <span class="private-sections__button-icon js-private-sections-icon"></span>
                    </a>
                    <header class="private-sections__head">
                        <a class="message-list__link" href="<?= $helper->url('user.message') ?>" target="_blank">Сообщения</a>
                    </header>

                    <div class="grid-scroll js-private-sections-body">
                        <ul class="grid-scroll-list message-list">
                            <li class="grid-scroll-list__item message-list__item message-list__item_new">
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
                    class="private-sections__item private-sections__item_ep grid__inner js-private-sections-container">
                    <a href="#" class="private-sections__button  js-private-sections-button">
                        <span class="private-sections__button-icon js-private-sections-icon"></span>
                    </a>

                    <header class="private-sections__head">Горящие фишки</header>

                    <div class="grid-scroll js-private-sections-body">
                        <ul class="grid-scroll-list grid-scroll-list_2col private-ep-list">
                        <? foreach ($coupons as $coupon): ?>
                        <?
                            $discount = $coupon->getDiscount();
                        ?>
                            <li class="grid-scroll-list__col js-ep-item">
                                <div class="grid-scroll-list__item private-ep-list__item">
                                    <span class="ep-coupon" style="background-image: url('<?= $coupon->getBackgroundImage() ?>');">
                                        <span class="ep-coupon__inner">
                                            <span class="ep-coupon__ico"><? if ($image = $coupon->getImage()): ?><img src="<?= $image ?>"><? endif ?></span>
                                            <span class="ep-coupon__desc"><?= $coupon->getName() ?></span>
                                            <span class="ep-coupon__price">
                                                <?= $helper->formatPrice($coupon->getPrice()) . (!$coupon->getIsCurrency() ? '%' : '') ?>
                                                <? if ($coupon->getIsCurrency()): ?>
                                                    <span class="rubl">p</span>
                                                <? endif ?>
                                            </span>
                                        </span>
                                    </span>

                                    <div class="private-ep-list__dutation js-coupon-date" data-value="<?= $helper->json(['date' => ($discount && ($endDate = $discount->getEndDate())) ? $endDate->getTimestamp() : null ]) ?>">
                                        <span data-name="day">3 д</span>
                                        <span data-name="hour">19 ч</span>
                                        <span data-name="minute">52 мин</span>
                                        <span data-name="second">51 с</span>
                                    </div>
                                </div>
                            </li>
                        <? endforeach ?>
                        </ul>

                        <? if (!$coupons): ?>
                        <ul class="grid-scroll-list grid-scroll-list_2col private-ep-list">
                            <li class="grid-scroll-list__col">
                                <div class="grid-scroll-list__item private-ep-list__item">
                                    <span class="ep-coupon"
                                          style="background-image: url('/styles/personal-page/img/fishki.png');">
                                    </span>
                                    <div class="private-ep-list__img-desc">Получи фишки EnterPrize</div>
                                </div>
                            </li>

                            <li class="grid-scroll-list__col">
                                <span class="private-ep-list__desc">
                                    Фишки EnterPrize используются для получения скидок. У каждой фишки свои условия и срок действия скидки. Использовать фишку можно только один раз, для этого нужно применить ее к заказу при оформлении. Узнай больше на странице EnterPrize.
                                </span>
                            </li>
                        </ul>
                        <? endif ?>

                    </div>
                </div>
            </div>

            <div class="private-ep-list__info clearfix">
                <div class="grid__cell grid__cell_2-big private-ep-list__item-info ep-info js-ep-item-info">
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

                    <div class="ep-info__product ep-info__product_big">
                        <div class="ep-info__row clearfix">
                            <h4 class="ep-info__product-title">
                                Действует на товары
                            </h4>

                            <a class="ep-info__product-all" href="#">
                                Посмотреть все
                            </a>
                        </div>
                        <div class=" js-slider-2 ep-info__product-slide" data-position="Basket"
                             data-slider="{&quot;limit&quot;:null,&quot;url&quot;:null,&quot;type&quot;:null,&quot;sender&quot;:{&quot;name&quot;:&quot;retailrocket&quot;,&quot;method&quot;:&quot;PersonalRecommendation&quot;,&quot;position&quot;:&quot;Basket&quot;,&quot;from&quot;:null},&quot;sender2&quot;:&quot;&quot;}">

                            <div class="goods-slider__inn ep-info__product-slide">
                                <div class="ep-info__product-list-outer">
                                    <ul class="goods-slider-list ep-info__product-list clearfix">
                                        <li class="goods-slider-list__i ep-info__product-item">
                                            <div class="ep-info__product-item-inner">
                                                <div class="ep-info__product-img-block">
                                                    <img src="#" alt="#">

                                                    <p class="ep-info__product-img-title">Lorem ipsum dolor sit amet,
                                                        consectetur
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
                                            </div>
                                        </li>

                                        <li class="goods-slider-list__i ep-info__product-item">
                                            <div class="ep-info__product-item-inner">
                                                <div class="ep-info__product-img-block">
                                                    <img src="#" alt="#">

                                                    <p class="ep-info__product-img-title">Lorem ipsum dolor sit amet,
                                                        consectetur
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
                                            </div>
                                        </li>

                                        <li class="goods-slider-list__i ep-info__product-item">
                                            <div class="ep-info__product-item-inner">
                                                <div class="ep-info__product-img-block">
                                                    <img src="#" alt="#">

                                                    <p class="ep-info__product-img-title">Lorem ipsum dolor sit amet,
                                                        consectetur
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
                                            </div>
                                        </li>

                                        <li class="goods-slider-list__i ep-info__product-item">
                                            <div class="ep-info__product-item-inner">
                                                <div class="ep-info__product-img-block">
                                                    <img src="#" alt="#">

                                                    <p class="ep-info__product-img-title">Lorem ipsum dolor sit amet,
                                                        consectetur
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
                                            </div>
                                        </li>

                                        <li class="goods-slider-list__i ep-info__product-item">
                                            <div class="ep-info__product-item-inner">
                                                <div class="ep-info__product-img-block">
                                                    <img src="#" alt="#">

                                                    <p class="ep-info__product-img-title">Lorem ipsum dolor sit amet,
                                                        consectetur
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
                                            </div>
                                        </li>

                                        <li class="goods-slider-list__i ep-info__product-item">
                                            <div class="ep-info__product-item-inner">
                                                <div class="ep-info__product-img-block">
                                                    <img src="#" alt="#">

                                                    <p class="ep-info__product-img-title">Lorem ipsum dolor sit amet,
                                                        consectetur
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
                                            </div>
                                        </li>

                                        <li class="goods-slider-list__i ep-info__product-item">
                                            <div class="ep-info__product-item-inner">
                                                <div class="ep-info__product-img-block">
                                                    <img src="#" alt="#">

                                                    <p class="ep-info__product-img-title">Lorem ipsum dolor sit amet,
                                                        consectetur
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
                                            </div>
                                        </li>

                                        <li class="goods-slider-list__i ep-info__product-item">
                                            <div class="ep-info__product-item-inner">
                                                <div class="ep-info__product-img-block">
                                                    <img src="#" alt="#">

                                                    <p class="ep-info__product-img-title">Lorem ipsum dolor sit amet,
                                                        consectetur
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
                                            </div>
                                        </li>

                                        <li class="goods-slider-list__i ep-info__product-item">
                                            <div class="ep-info__product-item-inner">
                                                <div class="ep-info__product-img-block">
                                                    <img src="#" alt="#">

                                                    <p class="ep-info__product-img-title">Lorem ipsum dolor sit amet,
                                                        consectetur
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
                                            </div>
                                        </li>

                                        <li class="goods-slider-list__i ep-info__product-item">
                                            <div class="ep-info__product-item-inner">
                                                <div class="ep-info__product-img-block">
                                                    <img src="#" alt="#">

                                                    <p class="ep-info__product-img-title">Lorem ipsum dolor sit amet,
                                                        consectetur
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
                                            </div>
                                        </li>

                                        <li class="goods-slider-list__i ep-info__product-item">
                                            <div class="ep-info__product-item-inner">
                                                <div class="ep-info__product-img-block">
                                                    <img src="#" alt="#">

                                                    <p class="ep-info__product-img-title">Lorem ipsum dolor sit amet,
                                                        consectetur
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
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div
                                    class="goods-slider__btn goods-slider__btn--prev ep-info__product-prev disabled mDisabled"></div>
                                <div class="goods-slider__btn goods-slider__btn--next ep-info__product-next"></div>
                            </div>


                        </div>
                    </div>
                </div>

                <div class="grid__cell grid__cell_2-big private-ep-list__item-info ep-info js-ep-item-info">
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

                    <div class="ep-info__product ep-info__product_big">
                        <div class="ep-info__row clearfix">
                            <h4 class="ep-info__product-title">
                                Действует на товары
                            </h4>

                            <a class="ep-info__product-all" href="#">
                                Посмотреть все
                            </a>
                        </div>
                        <div class=" js-slider-2 ep-info__product-slide" data-position="Basket"
                             data-slider="{&quot;limit&quot;:null,&quot;url&quot;:null,&quot;type&quot;:null,&quot;sender&quot;:{&quot;name&quot;:&quot;retailrocket&quot;,&quot;method&quot;:&quot;PersonalRecommendation&quot;,&quot;position&quot;:&quot;Basket&quot;,&quot;from&quot;:null},&quot;sender2&quot;:&quot;&quot;}">

                            <div class="goods-slider__inn ep-info__product-slide">
                                <div class="ep-info__product-list-outer">
                                    <ul class="goods-slider-list ep-info__product-list clearfix">
                                        <li class="goods-slider-list__i ep-info__product-item">
                                            <div class="ep-info__product-item-inner">
                                                <div class="ep-info__product-img-block">
                                                    <img src="#" alt="#">

                                                    <p class="ep-info__product-img-title">Lorem ipsum dolor sit amet,
                                                        consectetur
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
                                            </div>
                                        </li>

                                        <li class="goods-slider-list__i ep-info__product-item">
                                            <div class="ep-info__product-item-inner">
                                                <div class="ep-info__product-img-block">
                                                    <img src="#" alt="#">

                                                    <p class="ep-info__product-img-title">Lorem ipsum dolor sit amet,
                                                        consectetur
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
                                            </div>
                                        </li>

                                        <li class="goods-slider-list__i ep-info__product-item">
                                            <div class="ep-info__product-item-inner">
                                                <div class="ep-info__product-img-block">
                                                    <img src="#" alt="#">

                                                    <p class="ep-info__product-img-title">Lorem ipsum dolor sit amet,
                                                        consectetur
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
                                            </div>
                                        </li>

                                        <li class="goods-slider-list__i ep-info__product-item">
                                            <div class="ep-info__product-item-inner">
                                                <div class="ep-info__product-img-block">
                                                    <img src="#" alt="#">

                                                    <p class="ep-info__product-img-title">Lorem ipsum dolor sit amet,
                                                        consectetur
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
                                            </div>
                                        </li>

                                        <li class="goods-slider-list__i ep-info__product-item">
                                            <div class="ep-info__product-item-inner">
                                                <div class="ep-info__product-img-block">
                                                    <img src="#" alt="#">

                                                    <p class="ep-info__product-img-title">Lorem ipsum dolor sit amet,
                                                        consectetur
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
                                            </div>
                                        </li>

                                        <li class="goods-slider-list__i ep-info__product-item">
                                            <div class="ep-info__product-item-inner">
                                                <div class="ep-info__product-img-block">
                                                    <img src="#" alt="#">

                                                    <p class="ep-info__product-img-title">Lorem ipsum dolor sit amet,
                                                        consectetur
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
                                            </div>
                                        </li>

                                        <li class="goods-slider-list__i ep-info__product-item">
                                            <div class="ep-info__product-item-inner">
                                                <div class="ep-info__product-img-block">
                                                    <img src="#" alt="#">

                                                    <p class="ep-info__product-img-title">Lorem ipsum dolor sit amet,
                                                        consectetur
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
                                            </div>
                                        </li>

                                        <li class="goods-slider-list__i ep-info__product-item">
                                            <div class="ep-info__product-item-inner">
                                                <div class="ep-info__product-img-block">
                                                    <img src="#" alt="#">

                                                    <p class="ep-info__product-img-title">Lorem ipsum dolor sit amet,
                                                        consectetur
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
                                            </div>
                                        </li>

                                        <li class="goods-slider-list__i ep-info__product-item">
                                            <div class="ep-info__product-item-inner">
                                                <div class="ep-info__product-img-block">
                                                    <img src="#" alt="#">

                                                    <p class="ep-info__product-img-title">Lorem ipsum dolor sit amet,
                                                        consectetur
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
                                            </div>
                                        </li>

                                        <li class="goods-slider-list__i ep-info__product-item">
                                            <div class="ep-info__product-item-inner">
                                                <div class="ep-info__product-img-block">
                                                    <img src="#" alt="#">

                                                    <p class="ep-info__product-img-title">Lorem ipsum dolor sit amet,
                                                        consectetur
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
                                            </div>
                                        </li>

                                        <li class="goods-slider-list__i ep-info__product-item">
                                            <div class="ep-info__product-item-inner">
                                                <div class="ep-info__product-img-block">
                                                    <img src="#" alt="#">

                                                    <p class="ep-info__product-img-title">Lorem ipsum dolor sit amet,
                                                        consectetur
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
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div
                                    class="goods-slider__btn goods-slider__btn--prev ep-info__product-prev disabled mDisabled"></div>
                                <div class="goods-slider__btn goods-slider__btn--next ep-info__product-next"></div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>


            <div class="grid__cell">
                <div class="private-sections__item grid__inner js-private-sections-container">
                    <a href="#" class="private-sections__button  js-private-sections-button">
                        <span class="private-sections__button-icon js-private-sections-icon"></span>
                    </a>

                    <header class="private-sections__head">
                        <a class="private-sections__head-link" href="<?= $helper->url('user.favorites') ?>"
                           target="_blank">Избранное</a>
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

                        <!--Если нет избранного, вывести блок .item-none-->


                        <!--                                          <div class="item-none">
                                                                       <span class="item-none__txt">
                                                                           Добавляй товары в избранное
                                                                           <br>
                                                                           и узнавай об изменении цены и наличии
                                                                       </span>
                                                                   </div>-->

                    </div>
                </div>
            </div>

            <div class="grid__cell">
                <div class="private-sections__item grid__inner js-private-sections-container">
                    <a href="#" class="private-sections__button  js-private-sections-button">
                        <span class="private-sections__button-icon js-private-sections-icon"></span>
                    </a>

                    <header class="private-sections__head">
                        <a class="private-sections__head-link" href="<?= $helper->url('user.subscriptions') ?>"
                           target="_blank">Подписки</a>
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

    <? if (false): ?>
    <div class=" js-slider-2 viewed-slider" data-position="Basket"
         data-slider="{&quot;limit&quot;:null,&quot;url&quot;:null,&quot;type&quot;:null,&quot;sender&quot;:{&quot;name&quot;:&quot;retailrocket&quot;,&quot;method&quot;:&quot;PersonalRecommendation&quot;,&quot;position&quot;:&quot;Basket&quot;,&quot;from&quot;:null},&quot;sender2&quot;:&quot;&quot;}">

        <div class="viewed-slider__row clearfix">
            <h3 class="viewed-slider__title">
                Вы смотрели
            </h3>

            <? if (false): ?>
            <span class="viewed-slider__pagination">
                Страница <span class="js-viewed-slider-page">1</span> из <span class="js-viewed-slider-allPage">1</span>
            </span>
            <? endif ?>
        </div>

        <div class="goods-slider__inn viewed-slider__row">
            <ul class="goods-slider-list viewed-slider__list clearfix">

                <li class="goods-slider-list__i viewed-slider__item">
                    <div class="viewed-slider__item-inner">
                        <div class="viewed-slider__img-block">
                            <img src="#" alt="#">
                            <p class="viewed-slider__img-title">Lorem ipsum door sit amet, consectetur adipisicing.</p>
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
                        <div class="viewed-slider__buy">Купить</div>
                    </div>
                </li>

                <li class="goods-slider-list__i viewed-slider__item">
                    <div class="viewed-slider__item-inner">
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
                    </div>
                </li>

                <li class="goods-slider-list__i viewed-slider__item">
                    <div class="viewed-slider__item-inner">
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
                    </div>
                </li>

                <li class="goods-slider-list__i viewed-slider__item">
                    <div class="viewed-slider__item-inner">
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
                    </div>
                </li>

                <li class="goods-slider-list__i viewed-slider__item">
                    <div class="viewed-slider__item-inner">
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
                    </div>
                </li>

                <li class="goods-slider-list__i viewed-slider__item">
                    <div class="viewed-slider__item-inner">
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
                    </div>
                </li>

                <li class="goods-slider-list__i viewed-slider__item">
                    <div class="viewed-slider__item-inner">
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
                    </div>
                </li>

                <li class="goods-slider-list__i viewed-slider__item">
                    <div class="viewed-slider__item-inner">
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
                    </div>
                </li>

                <li class="goods-slider-list__i viewed-slider__item">
                    <div class="viewed-slider__item-inner">
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
                    </div>
                </li>

                <li class="goods-slider-list__i viewed-slider__item">
                    <div class="viewed-slider__item-inner">
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
                    </div>
                </li>

                <li class="goods-slider-list__i viewed-slider__item">
                    <div class="viewed-slider__item-inner">
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
                    </div>
                </li>

            </ul>

            <div class="goods-slider__btn goods-slider__btn--prev viewed-slider__prev disabled mDisabled"></div>
            <div class="goods-slider__btn goods-slider__btn--next viewed-slider__next"></div>
        </div>

        <?= $page->render('user/_menu', ['page' => $page]) ?>

        <?= $helper->render(
            'product-page/blocks/slider',
            [
                'type'           => 'main',
                'title'          => 'Мы рекомендуем',
                'products'       => $recommendedProducts,
                'limit'          => \App::config()->product['itemsInSlider'],
                'page'           => 1,
                'class'          => '',
                'sender'   => [
                    'name'     => 'retailrocket',
                    'position' => 'UserRecommended',
                    'method'   => 'PersonalRecommendation',
                ],
            ]
        ) ?>

        <?= $helper->render('product-page/blocks/slider', [
            'type'      => 'viewed',
            'title'     => 'Вы смотрели',
            'products'  => $viewedProducts,
            'limit'     => \App::config()->product['itemsInSlider'],
            'page'      => 1,
            'class'     => 'slideItem-7item goods-slider_mark',
        ]) ?>
    </div>
    <? endif ?>

</div>