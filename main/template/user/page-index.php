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
 * @var $onlinePaymentAvailableByNumberErp   bool[]
 * @var $paymentEntitiesByNumberErp          \Model\PaymentMethod\PaymentEntity[]
 */
?>

<?
$helper = new \Helper\TemplateHelper();
?>

<div class="personalPage personal" id="personal-container">

    <?= $page->render('user/_menu', ['page' => $page]) ?>

    <div class="private-sections grid js-ep-container">
        <div class="grid__col grid__col_2">
            <div class="grid__cell js-ep-item-top">
                <div
                    class="private-sections__item private-sections__item_order grid__inner js-private-sections-container">
                    <a href="#" class="private-sections__button  js-private-sections-button">
                        <span class="private-sections__button-icon js-private-sections-icon"></span>
                    </a>
                    <header class="private-sections__head js-ep-item-top-header">
                        <a class="private-sections__head-link" href="<?= $helper->url('user.orders') ?>" target="_blank">
                            Текущие заказы
                        </a>
                    </header>
                    <div class="grid-scroll js-private-sections-body">
                        <ul class="grid-scroll-list order-list">
                            <? foreach ($orders as $order): ?>
                            <?
                                $paymentContainerId = sprintf('order-paymentContainer-%s', md5($order->id . '-' . $order->numberErp));
                            ?>
                                <li class="grid-scroll-list__item order-list__item">
                                    <div class="order-list__data">
                                        <a class="order-list__data-number" href="<?= $helper->url('user.order', ['orderId' => $order->id]) ?>"><?= $order->numberErp ?></a>
                                        <div class="order-list__data-date"><?= $order->createdAt ? $order->createdAt->format('d.m.Y') : '' ?></div>
                                    </div>

                                    <div class="order-list__price">

                                        <? if ($order->totalPaySum): ?>
                                            <?= $helper->formatPrice($order->totalPaySum) ?> <span class="rubl">p</span>
                                        <? endif ?>
                                    </div>

                                    <div class="order-list__status">
                                        <? if ($status = $order->paymentStatus): ?>
                                            <div class="order-list__status-confrm"><?= $status->name ?></div>
                                        <? endif ?>

                                        <? if (!$order->isPaid() && $order->prepaidSum): ?>
                                            <div class="order-list__status-payment order-list__status-payment_warn">Требуется предоплата</div>
                                        <? elseif ($status = $order->status): ?>
                                            <div class="order-list__status-payment"><?= $status->name ?></div>
                                        <? endif ?>

                                        <? if (isset($onlinePaymentAvailableByNumberErp[$order->numberErp]) && $onlinePaymentAvailableByNumberErp[$order->numberErp]): ?>
                                            <a
                                                href="#"
                                                class="js-payment-popup-show order-list__pay"
                                                data-relation="<?= $helper->json(['container' => '.' . $paymentContainerId]) ?>"
                                            >Оплатить онлайн</a>
                                        <? endif ?>
                                    </div>

                                    <? if ($order->isCancelRequestAvailable): ?>
                                    <div class="order-list__toggler">
                                        <span class="order-list__toggler-txt">Еще</span>

                                        <div class="order-list__toggler-popup">
                                            <a
                                                href="#"
                                                class="js-orderCancel"
                                                data-value="<?= $helper->json([
                                                    'url'   => $helper->url('user.order.cancel'),
                                                    'order' => ['numberErp' => $order->numberErp, 'id' => $order->id],
                                                ]) ?>"
                                            >Отменить заказ</a>
                                        </div>
                                    </div>
                                    <? endif ?>

                                    <? if (!empty($paymentEntitiesByNumberErp[$order->numberErp])): ?>
                                        <div class="<?= $paymentContainerId ?>">
                                            <?= $helper->render('user/order/__onlinePayment-popup', ['order' => $order, 'paymentEntity' => $paymentEntitiesByNumberErp[$order->numberErp]]) ?>
                                        </div>
                                    <? endif ?>
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

                    <header class="private-sections__head">
                        <a class="private-sections__head-link" href="<?= $helper->url('user.address') ?>" target="_blank">
                            Адреса
                        </a>
                    </header>

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

                        <? if (!$addresses): ?>
                            <div class="item-none">
                                <span class="item-none__txt">Мы пока не знаем куда доставить ваши товары</span>
                                <a class="item-none__link" href="<?= $helper->url('user.address') ?>">Добавить адрес доставки</a>
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

                        <div class="item-none item-none_message">
                            <div class="item-none__img-block">
                                <img src="/styles/personal-page/img/no-message.png" alt="#">
                            </div>
                            <span class="item-none__txt">
                                У вас еще нет сообщений
                            </span>
                        </div>
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

                    <header class="private-sections__head">
                        <a class="private-sections__head-link" href="<?= $helper->url('user.enterprize') ?>" target="_blank">
                            Горящие фишки
                        </a>
                    </header>

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
                                    <a class="private-ep-list__link-block" href="<?= \App::router()->generate('enterprize') ?>" target="_blank">
                                        <div class="grid-scroll-list__item private-ep-list__item">
                                            <span class="ep-coupon" style="background-image: url('/styles/personal-page/img/fishki.png');"></span>
                                            <span class="private-ep-list__img-desc">Получи фишки EnterPrize</span>
                                        </div>
                                    </a>
                                </li>

                                <li class="grid-scroll-list__col">
                                    <a href="<?= \App::router()->generate('enterprize') ?>" target="_blank">
                                         <span class="private-ep-list__desc">
                                             Фишки EnterPrize используются для получения скидок. У каждой фишки свои условия и срок действия скидки. Использовать фишку можно только один раз, для этого нужно применить ее к заказу при оформлении. Узнай больше на странице EnterPrize.
                                         </span>
                                    </a>
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
                $linkName = $coupon->getLinkName() ? $coupon->getLinkName() : $coupon->getName();
            ?>
                <div class="grid__cell grid__cell_2-big private-ep-list__item-info ep-info js-ep-item-info">
                    <span class="ep-info__marker js-epInfoMarker"></span>
                    <div class="ep-info__desc">
                        <h4 class="ep-info__desc-title">
                            Фишка со скидкой <?= $helper->formatPrice($coupon->getPrice()) ?><?= !$coupon->getIsCurrency() ? '%' : ' <span class="rubl">p</span>' ?>
                            <? if ($linkName): ?><br /><?= ' на ' ?><?= $linkName ?><? endif ?>
                        </h4>

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
                        <p class="ep-info__desc-txt">
                            <?= $coupon->getSegmentDescription() ?>
                            Минимальная сумма заказа <?= $coupon->getMinOrderSum() ?: 0 ?> <span class="rubl">p</span>
                        </p>
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
                                        <? elseif ($product->isInShopShowroomOnly()) : ?>
                                            <div class="favorite-list__avail">На витрине</div>
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

    <script id="tpl-user-deleteOrderPopup" type="text/html" data-partial="<?= $helper->json([]) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/user/order/_deleteOrder-popup.mustache') ?>
    </script>
</div>