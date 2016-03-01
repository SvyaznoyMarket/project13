<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\OrderDelivery\Entity $orderDelivery
 * @param \Model\User\Address\Entity[] $userAddresses
 * @param \Model\OrderDelivery\UserInfoAddressAddition $userInfoAddressAddition
 * @param \Model\EnterprizeCoupon\Entity[] $userEnterprizeCoupons
 */
$f = function (
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity $orderDelivery,
    array $userAddresses = [],
    \Model\OrderDelivery\UserInfoAddressAddition $userInfoAddressAddition = null,
    array $userEnterprizeCoupons = []
) {
    $i = 0;
    $minQuantity = 1;
?>

    <? foreach ($orderDelivery->orders as $order): $i++; ?>
    <? if (false && (bool)$order->validationErrors) : ?>
        <!--<div class="jsOrderValidationErrors order-error order-error--warning" data-value="--><?//= $helper->json($order->validationErrors) ?><!--"> <i class="order-error__closer js-order-err-close"></i></div>-->
    <? endif ?>

        <!-- блок разбиения заказа -->
        <div class="orderRow order-bill__item clearfix jsOrderRow <?= $order->isPartnerOffer() ? 'jsPartnerOrder' : '' ?>"
             data-block_name="<?= $order->block_name ?>"
             data-is-delivery="<?= $order->delivery && $order->delivery->use_user_address ?>"
        >
            <!-- информация о заказе -->
            <div class="order-bill__head">Заказ №<?= ($i) ?></div>
            <a href="#" class="order-bill__later js-order-stash"  data-block_name="<?= $helper->escape($order->block_name) ?>">
                Оформить позже

                <span class="order-bill__later-info">
                    Вы можете отложить весь заказ в личный кабинет<br />
                    и продолжить оформление позже
                </span>
            </a>

            <!-- левая часть блока заказа - список заказанных товаров-->
            <div class="order-bill__wrap">
            <div class="order-bill__goods">
                <!-- введенные скидки, купоны -->
                <? if ((bool)$order->discounts || $order->certificate['par'] !== null) : ?>
                    <div class="payment-discount"><!-- контейнер купонов, max их может быть 2 (инфо от редакции)-->

                        <div class="order-discount__head">Скидки</div>

                        <? foreach ($order->discounts as $discount) : ?>
                            <div class="order-discount__item jsOrderV3Discount">
                                <div class="order-discount__cell">
                                    <? if (null !== $discount->number) : ?>
                                        <span class="order-discount__del jsDeleteDiscount" data-value="<?= $discount->number ?>">удалить</span>
                                    <? endif ?>
                                </div>
                                <? if ('online' === $discount->type): ?>
                                    <span class="order-discount__lk">
                                        <span class="order-discount__img order-discount__img_font">%</span>
                                    </span>
                                <? else: ?>
                                    <span class="order-discount__lk">
                                        <img class="order-discount__img" src="/styles/order-new/img/chip.png" alt="">
                                    </span>
                                <? endif ?>

                                <div class="order-discount__name">
                                    <?= $discount->name ?>
                                </div>

                                <div class="order-discount__val">
                                    -<?= $helper->formatPrice($discount->discount) ?>

                                    <? if ($discount->unit === 'rub'): ?>
                                        <span class="rubl">p</span>
                                    <? else: ?>
                                        <?= $helper->escape($discount->unit) ?>
                                    <? endif ?>
                                </div>
                            </div>
                        <? endforeach ?>

                        <? if ($order->certificate['par'] !== null) : ?>

                            <div class="order-discount__item clearfix">
                                <div class="order-discount__cell">
                                    <span class="order-discount__del jsDeleteCertificate">удалить</span>
                                </div>
                                <span class="order-discount__lk">
                                    <img class="order-discount__img" src="/styles/order/img/enter.png" alt="">
                                </span>

                                <div class="order-discount__name">Подарочный сертификат <?= $order->certificate['par'] ?> <span class="rubl">p</span></div>

                            <span
                                class="order-discount__val">-<?= $order->certificate['par'] ?>
                                <span class="rubl">p</span></span>
                            </div>
                        <? endif ?>
                    </div>
                <? endif ?>
                <!-- END: введенные скидки, купоны -->

                <div class="order-bill__body">
                    <? if ($order->seller): ?>
                        <div class="order-bill__seller">продавец: <?= $order->seller->name ?>
                            <a class="order-bill__oferta js-order-oferta-popup-btn" href="<?= $order->seller->offer ?>" data-value="<?= $order->seller->offer ?>" target="_blank">Информация и оферта</a>
                        </div>
                    <? endif ?>

                    <? foreach ($order->products as $product): ?>
                        <?= $helper->render('order-v3-new/partial/errors', ['orderDelivery' => $orderDelivery, 'order' => $order, 'product' => $product]) ?>

                        <div class="order-good__item">
                            <a href="<?= $product->link ?>" class="order-good__item-lk" target="_blank">
                                <img class="order-good__img" src="<?= $product->getMainImageUrl('product_60') ?>" alt="<?= $helper->escape($product->name) ?>" />
                            </a>

                            <a class="order-good__name" href="<?= $product->link ?>" target="_blank">
                                <? if ($product->prefix): ?><?= $product->prefix ?><br/><? endif ?>
                                <?= $product->name_web ?>
                            </a>

                            <div class="order-good__price-block">
                                <span class="order-good__price"><?= $helper->formatPrice($product->original_price) ?>
                                    <span class="rubl">p</span></span>
                                <span class="order-good__quantity js-show-edit"><?= $product->quantity ?> шт.</span>

                                <span class="order-good__total-price"><?= $helper->formatPrice($product->original_sum) ?>
                                    <span class="rubl">p</span>
                                </span>

                                <!-- редактирование кол-ва/удаление товара -->
                                <div class="order-good__edit js-edit" style="display: none">
                                    <div data-spinner-for="" class="order-good__count count">
                                        <button class="count__ctrl count__ctrl--less js-edit-quant js-edit-quant-decrease" title="Уменьшить" data-delta="-1" <? if ($product->quantity <= $minQuantity): ?>disabled="disabled"<? endif ?>>−</button>
                                        <input name="productQuantity[]" type="text" value="<?= $product->quantity ?>"
                                               class="count__num js-quant" data-stock="<?= $product->stock ?>"
                                               data-min="<?= $minQuantity ?>"/>
                                        <button class="count__ctrl count__ctrl--more js-edit-quant" title="Увеличить" data-delta="+1">+</button>
                                    </div>
                                    <span class="order-good__units">шт.</span>

                                    <a href="" class="order-good__apply jsChangeProductQuantity" data-ui="<?= $product->ui ?>" data-block_name="<?= $order->block_name ?>">Применить</a>
                                </div>
                                <!-- END редактирование кол-ва/удаление товара -->
                            </div>

                            <div class="order-good__more js-order-product-actions-dropbox-container">
                                <span class="order-good__more-btn js-order-product-actions-dropbox-opener"></span>
                                <ul class="order-good__more-list js-order-product-actions-dropbox-content">
                                    <li class="order-good__more-item js-order-product-actions-dropbox-item" data-action="favorite" data-product-ui="<?= $product->ui ?>" data-block_name="<?= $order->block_name ?>">Переместить в избранное</li>
                                    <li class="order-good__more-item js-order-product-actions-dropbox-item" data-action="delete" data-product-ui="<?= $product->ui ?>" data-block_name="<?= $order->block_name ?>">Удалить</li>
                                    <li class="order-good__more-item js-order-product-actions-dropbox-item">Отмена</li>
                                </ul>
                            </div>
                        </div>
                    <? endforeach ?>
                </div>
            </div>
            <!-- END левая часть блока заказа - список заказанных товаров-->


            <!-- правая часть блока заказа - выбор доставки/самовывоза -->
            <div class="order-bill__delivery-details">

                <!-- информация о доставке TODO: вынести блок в отдельный шаблон-->
                <menu class="order-delivery__menu">
                    <? foreach ($order->possible_delivery_groups as $deliveryGroup): ?>
                        <?
                            if ($order->is_free_delivery && ('1' === $deliveryGroup->id)) continue; // SITE-6537

                            // определение первого доступного delivery_method-а для группы
                            $delivery_methods_for_group = array_filter($order->possible_deliveries, function (\Model\OrderDelivery\Entity\DeliveryMethod $delivery) use ($deliveryGroup) {
                                return $delivery->group_id == $deliveryGroup->id;
                            });

                            /** @var \Model\OrderDelivery\Entity\DeliveryMethod $first_delivery_method */
                            $first_delivery_method = reset($delivery_methods_for_group);
                        ?>

                        <li class="order-delivery__type jsDeliveryChange <? if ($deliveryGroup->id == $order->delivery_group_id): ?>active<? endif ?>"
                            data-delivery_group_id="<?= $deliveryGroup->id ?>"
                            data-delivery_method_token="<?= (string)$first_delivery_method->token ?>"
                        >
                            <span class="order-delivery__type-inn"><?= $deliveryGroup->name ?></span>
                        </li>
                    <? endforeach ?>
                </menu>

                <div>
                    <? if (count($orderDelivery->orders) <= 1): ?>
                        <div class="order-region order-region_pickup"><?= $helper->escape(\App::user()->getRegion()->getName()) ?> <a href="#" class="order-region__change jsChangeRegion">Изменить</a></div>
                    <? endif ?>

                    <? if (!$order->delivery->use_user_address): ?>
                        <?= $helper->render('order-v3-new/partial/pickup', ['order' => $order, 'orderDelivery' => $orderDelivery]) ?>
                    <? else: ?>
                        <?= $helper->render('order-v3-new/partial/user-address', ['order' => $order, 'orderDelivery' => $orderDelivery, 'userAddresses' => $userAddresses, 'userInfoAddressAddition' => $userInfoAddressAddition]) ?>
                    <? endif ?>
                </div>

                <?= $helper->render('order-v3-new/__payment-methods', ['order' => $order]) ?>

                <?
                $dataPoints = (new \View\PointsMap\MapView());
                $dataPoints->preparePointsWithOrder($order, $orderDelivery);
                ?>

                <?= \App::templating()->render('order-v3/common/_map', [
                    'dataPoints' => $dataPoints,
                    'page' => 'order',
                    'enableFitsAllProducts' => count($order->uniqueFitsAllProductsValuesOfPoints) > 1 ? true : false,
                ]) ?>

                <!--/ информация о доставке -->

            </div>
            <!-- END правая часть блока заказа - выбор доставки/самовывоза -->

            </div>

            <!-- ввести код скидки -->
            <div class="order-bill__adds">

                <div class="order-bill__total">
                    <span class="order-bill__total-price"><?= $order->delivery->price == 0 ? 'Бесплатно' : $helper->formatPrice($order->delivery->price) . ' <span class="rubl">p</span>' ?></span>
                    <span class="order-bill__serv"><?= $order->delivery->use_user_address ? 'Доставка' : 'Самовывоз' ?>:</span>
                    <span class="order-bill__total-price"><?= $helper->formatPrice($order->total_cost) ?> <span class="rubl">p</span></span>
                    <span class="order-bill__serv">Итого: </span>
                </div>

                <?= $helper->render('order-v3-new/partial/discount', ['order' => $order, 'userEnterprizeCoupons' => $userEnterprizeCoupons]) ?>

            </div>
            <!-- END ввести код скидки -->

            <!--/ информация о заказе -->
        </div>
        <!--/ блок разбиения заказа -->
    <? endforeach ?>

<? }; return $f;
