<?php
/**
 * @var $page                          \View\User\OrderPage
 * @var $user                          \Session\User
 * @var $orders                        \Model\Order\Entity[]
 * @var $coupons                       \Model\EnterprizeCoupon\Entity[]
 * @var $addresses                     \Model\User\Address\Entity[]
 * @var $product                       \Model\Product\Entity|null
 * @var $favoriteProductsByUi          \Model\Favorite\Product\Entity[]
 * @var $channelsById                  \Model\Subscribe\Channel\Entity[]
 * @var $subscription                  \Model\User\SubscriptionEntity
 * @var $subscriptionsGroupedByChannel array
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
                                    <a class="address-list__item-link" href="<?= $helper->url('user.address') ?>"
                                       target="_blank">
                                        <? if (false && $address->description): ?>
                                            <div class="address-list__mode"><?= $helper->escape($address->description) ?></div><? endif ?>
                                        <ul class="address-list-details">
                                            <? if ($address->region): ?>
                                                <li class="address-list-details__item"><?= $address->region->name ?></li><? endif ?>
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
                        <? if (false): ?>
                            <ul class="grid-scroll-list message-list">
                                <li class="grid-scroll-list__item message-list__item message-list__item_new">
                                    <a class="message-list__link clearfix" href="#" target="_blank">
                                        <div class="message-list__left">
                                            <div class="message-list__title">
                                                Заказ #COXD-123456
                                            </div>

                                            <p class="message-list__text">
                                                Ваш заказ передан в транспортную компанию!
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
                        <? endif ?>
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
                                $sliderContainerId = sprintf('id-coupon-container-%s', $coupon->getToken() ? md5($coupon->getToken()) : uniqid());
                                $discount = $coupon->getDiscount();
                            ?>
                                <li
                                    class="grid-scroll-list__col js-ep-item"
                                    data-slider="<?= $helper->json([
                                        'url' => $helper->url('enterprize.slider', ['enterprizeToken' => $coupon->getToken(), 'template' => 'user']),
                                    ]) ?>"
                                    data-relation="<?= $helper->json([
                                        'container' => '.' . $sliderContainerId,
                                    ]) ?>"
                                >
                                    <div class="grid-scroll-list__item private-ep-list__item">
                                        <span class="ep-coupon"
                                              style="background-image: url('<?= $coupon->getBackgroundImage() ?>');">
                                            <span class="ep-coupon__inner">
                                                <span class="ep-coupon__ico"><? if ($image = $coupon->getImage()): ?><img src="<?= $image ?>"><? endif ?></span>
                                                <span class="ep-coupon__desc"><?= $coupon->getName() ?></span>
                                                <span class="ep-coupon__price">
                                                    <?= $helper->formatPrice($coupon->getPrice()) . (!$coupon->getIsCurrency() ? '%' : '') ?>
                                                    <? if ($coupon->getIsCurrency()): ?><span class="rubl">p</span><? endif ?>
                                                </span>
                                            </span>
                                        </span>

                                        <div class="private-ep-list__dutation js-countdown-out js-countdown" data-expires="<?= (($discount && $discount->getEndDate()) ? $discount->getEndDate()->getTimestamp() : null) ?>"></div>
                                    </div>
                                </li>
                            <? endforeach ?>

                            <? if (!$coupons): ?>
                            <li class="grid-scroll-list__col">
                                <div class="grid-scroll-list__item private-ep-list__item">
                                    <span class="ep-coupon" style="background-image: url('/styles/personal-page/img/fishki.png');"></span>
                                    <a href="<?= $helper->url('enterprize') ?>" class="private-ep-list__img-desc">Получи фишки EnterPrize</a>
                                </div>
                            </li>

                            <li class="grid-scroll-list__col">
                                 <span class="private-ep-list__desc">
                                     Фишки EnterPrize используются для получения скидок. У каждой фишки свои условия и срок действия скидки. Использовать фишку можно только один раз, для этого нужно применить ее к заказу при оформлении. Узнай больше на странице EnterPrize.
                                 </span>
                            </li>
                            <? endif ?>
                        </ul>
                    </div>
                </div>


            </div>

            <div class="private-ep-list__info clearfix">
            <? foreach ($coupons as $coupon): ?>
            <?
                $sliderContainerId = sprintf('id-coupon-container-%s', $coupon->getToken() ? md5($coupon->getToken()) : uniqid());
                $discount = $coupon->getDiscount();
            ?>
                <div class="grid__cell grid__cell_2-big private-ep-list__item-info ep-info js-ep-item-info">
                    <span class="ep-info__marker js-epInfoMarker"></span>
                    <div class="ep-info__desc">
                        <h4 class="ep-info__desc-title"><?= $coupon->getName() ?></h4>

                        <div class="ep-info__desc-duration">
                        <? if ($date = $coupon->getStartDate()): ?>
                            с <span class="ep-info__desc-duration-start"><?= $date->format('d.m.Y') ?></span>
                        <? endif ?>
                        <? if ($date = $coupon->getEndDate()): ?>
                            по <span class="ep-info__desc-duration-end"><?= $date->format('d.m.Y') ?></span>
                        <? endif ?>
                        </div>
                        <div class="ep-info__desc-timer">
                            <p class="ep-info__desc-timer-desc">До конца действия осталось</p>
                            <div class="ep-info__desc-timer-report js-countdown-out js-countdown" data-expires="<?= (($discount && $discount->getEndDate()) ? $discount->getEndDate()->getTimestamp() : null) ?>"></div>
                        </div>
                        <p class="ep-info__desc-txt"><?= $coupon->setSegmentDescription() ?></p>
                    </div>

                    <div class="ep-info__product ep-info__product_big">
                        <div class="ep-info__row clearfix">
                            <h4 class="ep-info__product-title">
                                Действует на товары
                            </h4>

                            <? if ($coupon->getLink()): ?>
                            <a class="ep-info__product-all" href="<?= $coupon->getLink() ?>">Посмотреть все</a>
                            <? endif ?>
                        </div>
                        <div class="<?= $sliderContainerId ?> js-user-slider-container ep-info__product-slide" data-position="Private.enterprize"></div>
                    </div>
                </div>
            <? endforeach ?>
            </div>

            <div class="grid__cell">
                <div class="private-sections__item grid__inner js-private-sections-container">
                    <a href="#" class="private-sections__button  js-private-sections-button">
                        <span class="private-sections__button-icon js-private-sections-icon"></span>
                    </a>

                    <header class="private-sections__head">
                        <a class="private-sections__head-link" href="<?= $helper->url('user.favorites') ?>" target="_blank">Избранное</a>
                    </header>

                    <div class="grid-scroll js-private-sections-body">
                        <ul class="grid-scroll-list favorite-list">
                            <? foreach ($favoriteProductsByUi as $favoriteProduct): ?>
                                <?
                                /** @var \Model\Product\Entity|null $product */
                                if (!$product = (isset($productsByUi[$favoriteProduct->ui]) ? $productsByUi[$favoriteProduct->ui] : null)) continue;
                                ?>
                                <li class="grid-scroll-list__item favorite-list__item">
                                <span class="favorite-list__link">
                                    <div class="favorite-list__views favorite-list__cell">
                                        <a href="<?= $product->getLink() ?>" target="_blank">
                                            <img src="<?= $product->getImageUrl(1) ?>" class="image" alt="<?= $helper->escape($product->getName()) ?>">
                                        </a>
                                    </div>

                                    <div class="favorite-list__cell">
                                        <a href="<?= $product->getLink() ?>"
                                           target="_blank"><?= $helper->escape($product->getName()) ?></a>
                                        <? if ($product->getIsBuyable()): ?>
                                            <div class="favorite-list__avail">В наличии</div>
                                        <? else: ?>
                                            <div class="favorite-list__avail favorite-list__avail_no">Нет в наличии
                                            </div>
                                        <? endif ?>
                                    </div>

                                    <div class="favorite-list__price favorite-list__cell">
                                        <? if ($product->getPrice()): ?>
                                            <?= $helper->formatPrice($product->getPrice()) ?> <span class="rubl">p</span>
                                        <? endif ?>
                                    </div>
                                </span>
                                </li>
                            <? endforeach ?>
                        </ul>

                        <? if (!$favoriteProductsByUi): ?>
                        <div class="item-none">
                            <span class="item-none__txt">
                            Добавляй товары в избранное
                            <br>
                            и узнавай об изменении цены и наличии
                            </span>
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
                        <a class="private-sections__head-link" href="<?= $helper->url('user.subscriptions') ?>" target="_blank">Подписки</a>
                    </header>

                    <div class="grid-scroll js-private-sections-body">
                        <ul class="grid-scroll-list subscribe-list">
                        <? $i = 0; foreach ($channelsById as $channel): $i++ ?>
                        <?
                            $subscription = isset($subscriptionsGroupedByChannel[$channel->id]) ? $subscriptionsGroupedByChannel[$channel->id] : null;
                            if (!$channel->isActive && !$subscription) continue;

                            $elementId = sprintf('channel-%s', md5(json_encode($channel, JSON_UNESCAPED_UNICODE)));
                        ?>
                            <li class="grid-scroll-list__item subscribe-list__item">
                                <input
                                    class="js-user-subscribe-input customInput customInput-checkbox"
                                    id="<?= $elementId ?>"
                                    type="checkbox"
                                    name="channel[<?= $i ?>]"
                                    <?= $subscription ? 'checked' : '' ?>
                                    data-set-url="<?= $page->url('user.subscriptions') ?>"
                                    data-delete-url="<?= $page->url('user.subscriptions', ['delete' => true]) ?>"
                                    data-value="<?= $page->json([
                                        'subscribe' => [
                                            'channel_id' => $channel->id,
                                            'type'       => 'email',
                                            'email'      => $user->getEntity()->getEmail(),
                                        ]
                                    ])?>"
                                />
                                <label class="customLabel" for="<?= $elementId ?>"><?= $channel->name ?></label>
                            </li>
                        <? endforeach ?>

                            <? if (false): ?>
                            <li class="grid-scroll-list__item subscribe-list__item">
                                <input class="customInput customInput-checkbox" type="checkbox" id="subscribe-ep-2" disabled/>
                                <label class="customLabel" for="subscribe-ep-2">Новости EnterPrize 1</label>
                            </li>
                            <? endif ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div>
        <?= $helper->render('product/__slider', [
            'type'           => 'personal',
            'title'          => 'Мы рекомендуем',
            'products'       => [],
            'url'       => $page->url('recommended', [
                'types'  => ['personal'],
                'sender' => [
                    'position' => 'Basket',
                ],
                'showLimit' => 6,
            ]),
        ]) ?>
    </div>

    <div>
    <?= $helper->render('product/__slider', [
        'type'      => 'viewed',
        'title'     => 'Вы смотрели',
        'products'  => [],
        'limit'     => \App::config()->product['itemsInSlider'],
        'page'      => 1,
        'url'       => $page->url('product.recommended'),
        'class'     => 'slideItem-viewed',
        'isCompact' => true,
        'sender'    => [
            'name'     => 'enter',
            'position' => 'Viewed',
            'from'     => 'categoryPage'
        ],
    ]) ?>
    </div>

    <?//= $page->render('user/_menu', ['page' => $page]) ?>


    <? if (false): ?>
        <div class=" js-slider-2 viewed-slider" data-position="Basket"
             data-slider="{&quot;limit&quot;:null,&quot;url&quot;:null,&quot;type&quot;:null,&quot;sender&quot;:{&quot;name&quot;:&quot;retailrocket&quot;,&quot;method&quot;:&quot;PersonalRecommendation&quot;,&quot;position&quot;:&quot;Basket&quot;,&quot;from&quot;:null},&quot;sender2&quot;:&quot;&quot;}">

            <div class="viewed-slider__row clearfix">
                <h3 class="viewed-slider__title">Вы смотрели</h3>
                <span class="viewed-slider__pagination">Страница <span class="js-viewed-slider-page">1</span> из <span class="js-viewed-slider-allPage">1</span></span>
            </div>

            <div class="goods-slider__inn viewed-slider__row">
                <ul class="goods-slider-list viewed-slider__list clearfix">
                    <li class="goods-slider-list__i viewed-slider__item">
                        <div class="viewed-slider__item-inner">
                            <div class="viewed-slider__img-block">
                                <img src="#" alt="#">

                                <p class="viewed-slider__img-title">Lorem ipsum door sit amet, consectetur
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
        </div>
    <? endif ?>

</div>