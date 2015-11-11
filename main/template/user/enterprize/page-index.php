<?php
/**
 * @var $page                              \View\User\OrdersPage
 * @var $helper                            \Helper\TemplateHelper
 * @var $user                              \Session\User
 * @var $orderCount                        int
 * @var $ordersByYear                      array
 * @var $orders                            \Model\User\Order\Entity[]
 * @var $orderProduct                      \Model\Order\Product\Entity|null
 * @var $product                           \Model\Product\Entity|null
 * @var $productsById                      \Model\Product\Entity[]
 * @var $point                             \Model\Point\PointEntity
 * @var $pointsByUi                        \Model\Point\PointEntity[]
 * @var $onlinePaymentAvailableByNumberErp bool[]
 * @var $viewedProducts                    \Model\Product\Entity[]
 */
?>

<div class="personal">
    <?= $page->render('user/_menu-1508', ['page' => $page]) ?>

    <div class="personalPage">

        <div class="private-sections private-sections_gray private-sections_p20 grid ">
            <div class="js-ep-container">
                <ul class="private-ep-list clearfix">
                    <li class="private-ep-list__cell js-ep-item">
                        <div class="private-ep-list__item ">
                                <span class="ep-coupon"
                                      style="background-image: url(http://content.enter.ru/wp-content/uploads/2014/03/fishka_orange_b1.png);">
                                    <span class="ep-coupon__inner">
                                        <span class="ep-coupon__ico"><img
                                                src="http://content.enter.ru/wp-content/uploads/2014/03/bit.png"></span>

                                        <span class="ep-coupon__desc">Крупногабаритная и встраиваемая техника</span>

                                        <span class="ep-coupon__price">5%</span>
                                    </span>
                                </span>
                        </div>
                    </li>

                    <li class="private-ep-list__cell js-ep-item">
                        <div class="private-ep-list__item ">
                                <span class="ep-coupon"
                                      style="background-image: url(http://content.enter.ru/wp-content/uploads/2014/03/fishka_orange_b1.png);">
                                    <span class="ep-coupon__inner">
                                        <span class="ep-coupon__ico"><img
                                                src="http://content.enter.ru/wp-content/uploads/2014/03/bit.png"></span>

                                        <span class="ep-coupon__desc">Крупногабаритная и встраиваемая техника</span>

                                        <span class="ep-coupon__price">5%</span>
                                    </span>
                                </span>
                        </div>
                    </li>

                    <li class="private-ep-list__cell js-ep-item">
                        <div class="private-ep-list__item ">
                                <span class="ep-coupon"
                                      style="background-image: url(http://content.enter.ru/wp-content/uploads/2014/03/fishka_orange_b1.png);">
                                    <span class="ep-coupon__inner">
                                        <span class="ep-coupon__ico"><img
                                                src="http://content.enter.ru/wp-content/uploads/2014/03/bit.png"></span>

                                        <span class="ep-coupon__desc">Крупногабаритная и встраиваемая техника</span>

                                        <span class="ep-coupon__price">5%</span>
                                    </span>
                                </span>
                        </div>
                    </li>

                    <li class="private-ep-list__cell js-ep-item">
                        <div class="private-ep-list__item ">
                                <span class="ep-coupon"
                                      style="background-image: url(http://content.enter.ru/wp-content/uploads/2014/03/fishka_orange_b1.png);">
                                    <span class="ep-coupon__inner">
                                        <span class="ep-coupon__ico"><img
                                                src="http://content.enter.ru/wp-content/uploads/2014/03/bit.png"></span>

                                        <span class="ep-coupon__desc">Крупногабаритная и встраиваемая техника</span>

                                        <span class="ep-coupon__price">5%</span>
                                    </span>
                                </span>
                        </div>
                    </li>

                </ul>

                <div class="private-ep-list__info clearfix">
                    <div class="grid__cell grid__cell_2 ep-info js-ep-item-info">
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
                            <div class=" js-slider-2 ep-info__product-slide" data-position="Basket"
                                 data-slider="{&quot;limit&quot;:null,&quot;url&quot;:null,&quot;type&quot;:null,&quot;sender&quot;:{&quot;name&quot;:&quot;retailrocket&quot;,&quot;method&quot;:&quot;PersonalRecommendation&quot;,&quot;position&quot;:&quot;Basket&quot;,&quot;from&quot;:null},&quot;sender2&quot;:&quot;&quot;}">

                                <div class="goods-slider__inn ep-info__product-slide">
                                    <div class="ep-info__product-list-outer">
                                        <ul class="goods-slider-list ep-info__product-list clearfix">
                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                    <div class="grid__cell grid__cell_2 ep-info js-ep-item-info">
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
                            <div class=" js-slider-2 ep-info__product-slide" data-position="Basket"
                                 data-slider="{&quot;limit&quot;:null,&quot;url&quot;:null,&quot;type&quot;:null,&quot;sender&quot;:{&quot;name&quot;:&quot;retailrocket&quot;,&quot;method&quot;:&quot;PersonalRecommendation&quot;,&quot;position&quot;:&quot;Basket&quot;,&quot;from&quot;:null},&quot;sender2&quot;:&quot;&quot;}">

                                <div class="goods-slider__inn ep-info__product-slide">
                                    <div class="ep-info__product-list-outer">
                                        <ul class="goods-slider-list ep-info__product-list clearfix">
                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                    <div class="grid__cell grid__cell_2 ep-info js-ep-item-info">
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
                            <div class=" js-slider-2 ep-info__product-slide" data-position="Basket"
                                 data-slider="{&quot;limit&quot;:null,&quot;url&quot;:null,&quot;type&quot;:null,&quot;sender&quot;:{&quot;name&quot;:&quot;retailrocket&quot;,&quot;method&quot;:&quot;PersonalRecommendation&quot;,&quot;position&quot;:&quot;Basket&quot;,&quot;from&quot;:null},&quot;sender2&quot;:&quot;&quot;}">

                                <div class="goods-slider__inn ep-info__product-slide">
                                    <div class="ep-info__product-list-outer">
                                        <ul class="goods-slider-list ep-info__product-list clearfix">
                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                    <div class="grid__cell grid__cell_2 ep-info js-ep-item-info">
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
                            <div class=" js-slider-2 ep-info__product-slide" data-position="Basket"
                                 data-slider="{&quot;limit&quot;:null,&quot;url&quot;:null,&quot;type&quot;:null,&quot;sender&quot;:{&quot;name&quot;:&quot;retailrocket&quot;,&quot;method&quot;:&quot;PersonalRecommendation&quot;,&quot;position&quot;:&quot;Basket&quot;,&quot;from&quot;:null},&quot;sender2&quot;:&quot;&quot;}">

                                <div class="goods-slider__inn ep-info__product-slide">
                                    <div class="ep-info__product-list-outer">
                                        <ul class="goods-slider-list ep-info__product-list clearfix">
                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

            </div>

            <div class="js-ep-container">
                <ul class="private-ep-list clearfix">
                    <li class="private-ep-list__cell js-ep-item">
                        <div class="private-ep-list__item ">
                                <span class="ep-coupon"
                                      style="background-image: url(http://content.enter.ru/wp-content/uploads/2014/03/fishka_orange_b1.png);">
                                    <span class="ep-coupon__inner">
                                        <span class="ep-coupon__ico"><img
                                                src="http://content.enter.ru/wp-content/uploads/2014/03/bit.png"></span>

                                        <span class="ep-coupon__desc">Крупногабаритная и встраиваемая техника</span>

                                        <span class="ep-coupon__price">5%</span>
                                    </span>
                                </span>
                        </div>
                    </li>

                    <li class="private-ep-list__cell js-ep-item">
                        <div class="private-ep-list__item ">
                                <span class="ep-coupon"
                                      style="background-image: url(http://content.enter.ru/wp-content/uploads/2014/03/fishka_orange_b1.png);">
                                    <span class="ep-coupon__inner">
                                        <span class="ep-coupon__ico"><img
                                                src="http://content.enter.ru/wp-content/uploads/2014/03/bit.png"></span>

                                        <span class="ep-coupon__desc">Крупногабаритная и встраиваемая техника</span>

                                        <span class="ep-coupon__price">5%</span>
                                    </span>
                                </span>
                        </div>
                    </li>

                    <li class="private-ep-list__cell js-ep-item">
                        <div class="private-ep-list__item ">
                                <span class="ep-coupon"
                                      style="background-image: url(http://content.enter.ru/wp-content/uploads/2014/03/fishka_orange_b1.png);">
                                    <span class="ep-coupon__inner">
                                        <span class="ep-coupon__ico"><img
                                                src="http://content.enter.ru/wp-content/uploads/2014/03/bit.png"></span>

                                        <span class="ep-coupon__desc">Крупногабаритная и встраиваемая техника</span>

                                        <span class="ep-coupon__price">5%</span>
                                    </span>
                                </span>
                        </div>
                    </li>

                    <li class="private-ep-list__cell js-ep-item">
                        <div class="private-ep-list__item ">
                                <span class="ep-coupon"
                                      style="background-image: url(http://content.enter.ru/wp-content/uploads/2014/03/fishka_orange_b1.png);">
                                    <span class="ep-coupon__inner">
                                        <span class="ep-coupon__ico"><img
                                                src="http://content.enter.ru/wp-content/uploads/2014/03/bit.png"></span>

                                        <span class="ep-coupon__desc">Крупногабаритная и встраиваемая техника</span>

                                        <span class="ep-coupon__price">5%</span>
                                    </span>
                                </span>
                        </div>
                    </li>

                </ul>

                <div class="private-ep-list__info clearfix">
                    <div class="grid__cell grid__cell_2 ep-info js-ep-item-info">
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
                            <div class=" js-slider-2 ep-info__product-slide" data-position="Basket"
                                 data-slider="{&quot;limit&quot;:null,&quot;url&quot;:null,&quot;type&quot;:null,&quot;sender&quot;:{&quot;name&quot;:&quot;retailrocket&quot;,&quot;method&quot;:&quot;PersonalRecommendation&quot;,&quot;position&quot;:&quot;Basket&quot;,&quot;from&quot;:null},&quot;sender2&quot;:&quot;&quot;}">

                                <div class="goods-slider__inn ep-info__product-slide">
                                    <div class="ep-info__product-list-outer">
                                        <ul class="goods-slider-list ep-info__product-list clearfix">
                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                    <div class="grid__cell grid__cell_2 ep-info js-ep-item-info">
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
                            <div class=" js-slider-2 ep-info__product-slide" data-position="Basket"
                                 data-slider="{&quot;limit&quot;:null,&quot;url&quot;:null,&quot;type&quot;:null,&quot;sender&quot;:{&quot;name&quot;:&quot;retailrocket&quot;,&quot;method&quot;:&quot;PersonalRecommendation&quot;,&quot;position&quot;:&quot;Basket&quot;,&quot;from&quot;:null},&quot;sender2&quot;:&quot;&quot;}">

                                <div class="goods-slider__inn ep-info__product-slide">
                                    <div class="ep-info__product-list-outer">
                                        <ul class="goods-slider-list ep-info__product-list clearfix">
                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                    <div class="grid__cell grid__cell_2 ep-info js-ep-item-info">
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
                            <div class=" js-slider-2 ep-info__product-slide" data-position="Basket"
                                 data-slider="{&quot;limit&quot;:null,&quot;url&quot;:null,&quot;type&quot;:null,&quot;sender&quot;:{&quot;name&quot;:&quot;retailrocket&quot;,&quot;method&quot;:&quot;PersonalRecommendation&quot;,&quot;position&quot;:&quot;Basket&quot;,&quot;from&quot;:null},&quot;sender2&quot;:&quot;&quot;}">

                                <div class="goods-slider__inn ep-info__product-slide">
                                    <div class="ep-info__product-list-outer">
                                        <ul class="goods-slider-list ep-info__product-list clearfix">
                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                    <div class="grid__cell grid__cell_2 ep-info js-ep-item-info">
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
                            <div class=" js-slider-2 ep-info__product-slide" data-position="Basket"
                                 data-slider="{&quot;limit&quot;:null,&quot;url&quot;:null,&quot;type&quot;:null,&quot;sender&quot;:{&quot;name&quot;:&quot;retailrocket&quot;,&quot;method&quot;:&quot;PersonalRecommendation&quot;,&quot;position&quot;:&quot;Basket&quot;,&quot;from&quot;:null},&quot;sender2&quot;:&quot;&quot;}">

                                <div class="goods-slider__inn ep-info__product-slide">
                                    <div class="ep-info__product-list-outer">
                                        <ul class="goods-slider-list ep-info__product-list clearfix">
                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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

                                            <li class="goods-slider-list__i ep-info__product-item ep-info__product-item_min">
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
            </div>
        </div>

    </div>

</div>
