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
 */
?>

<?
$showStatus = \App::user()->getEntity() && in_array(\App::user()->getEntity()->getId(), ['1019768', '104406', '1036742', '764984', '395421', '180860', '197474', '54', '325127', '641265', '11446', '11447']);

$currentYear = (int)(new \DateTime())->format('Y');

$prepaymentPriceLimit = \App::config()->order['prepayment']['enabled'] ? \App::config()->order['prepayment']['priceLimit'] : null;
?>

<div class="personal">
    <?= $page->render('user/_menu-1508', ['page' => $page]) ?>

    <? foreach ($ordersByYear as $year => $orders): ?>
    <?
        $containerId = 'id-orderContainer-' . $year;
        $count = count($orders);
    ?>

        <div class="personal__orders <? if ($currentYear === $year): ?>current<? endif ?>">
            <div class="personal__orders-head"><?= (($currentYear === $year) ? 'Текущие заказы' : 'История заказов') ?></div>
            <div class="<?= $containerId ?> personal-order__block <?= ($currentYear == $year ? 'expanded' : '') ?>">
                <? if ($currentYear !== $year): ?>
                <span class="personal-order__year-container">
                   <span class="personal-order__year js-orderContainer-link" data-relation="<?= $helper->json(['container' => '.' . $containerId]) ?>"> <?= $year ?></span>
                </span>
                <span class="personal-order__year-total"><?= ($count . ' ' . $helper->numberChoice($count, ['заказ', 'заказа', 'заказов'])) ?></span>
                <? endif ?>

                <? foreach ($orders as $order): ?>
                    <div class="personal-order__item">
                        <div class="personal-order__cell">
                            <a class="personal-order__num" href="<?= $page->url('user.order', ['orderId' => $order->id ]) ?>"><?= $order->numberErp ?></a>
                            <span class="personal-order__date"><?= $order->createdAt ? $order->createdAt->format('d.m.Y') : '' ?></span>
                        </div>
                        <div class="personal-order__cell">
                            <? if (($orderProduct = reset($order->product) ?: null) && ($product = @$productsById[$orderProduct->getId()] ?: null)): ?>
                                <div class="personal-order__name ellipsis"><?= $product->getName() ?> <?= $orderProduct->getQuantity() ?> шт</div>

                                <? if ($moreProductCount = (count($order->product) - 1)): ?>
                                    <div style="color: #868686">и еще <?= $moreProductCount . ' ' . $helper->numberChoice($moreProductCount, ['товар', 'товара', 'товаров']) ?></div>
                                <? endif ?>
                            <? endif ?>
                            <span class="personal-order__info warning">
                            <? if ((null !== $prepaymentPriceLimit) && ($order->sum >= $prepaymentPriceLimit)): ?>
                                Требуется предоплата
                            <? endif ?>
                            </span>
                        </div>
                        <div class="personal-order__cell">
                            <span class="personal-order__deliv-type">
                                <?= $order->getDeliveryTypeName() ?>
                                <? if ($order->getDelivery() && ($deliveredAt = $order->getDelivery()->getDeliveredAt())): ?>
                                    <?= $deliveredAt->format('d.m.Y') ?>
                                <? endif ?>
                            </span>
                            <div class="personal-order__deliv-info ellipsis">
                                <? if ($order->pointUi && ($point = $pointsByUi[$order->pointUi])): ?>
                                    <?= $point->getTypeName() ?><br><?= $point->address ?>
                                <? endif ?>
                                <? if ($order->getDelivery()->isShipping) : ?>
                                    <?= $order->address ?>
                                <? endif ?>
                            </div>
                        </div>
                        <div class="personal-order__cell personal-order__price">
                            <?= $helper->formatPrice($order->getSum()) ?> <span class="rubl">p</span>
                        </div>
                        <div class="personal-order__cell">
                            <? if ($showStatus): ?>
                                <span class="personal-order__status"><?= $order->getLastLifecycleStatus() ?></span>
                                <? if (isset($onlinePaymentAvailableByNumberErp[$order->numberErp]) && $onlinePaymentAvailableByNumberErp[$order->numberErp]): ?>
                                    <!--<span class="personal-order__pay-status online">Оплатить онлайн</span>-->
                                <? endif ?>
                            <? endif ?>
                        </div>
                        <div class="personal-order__cell">
                            <!--
                            <span class="personal-order__more">Еще
                                <div class="personal-order__cancel">Повторить заказ</div>
                            </span>
                            -->
                        </div>
                    </div>
                <? endforeach ?>
            </div>
        </div>
    <? endforeach ?>

    <? if (false): ?>
    <div class="personal__orders">
        <div class="personal-order__block expanded">
                <span class="personal-order__year-container">
                   <span class="personal-order__year"> 2015</span>
                </span><span class="personal-order__year-total">5 заказов</span>

            <div class="personal-order__item">
                <div class="personal-order__cell">
                    <span class="personal-order__num">COXF-767608</span>
                    <span class="personal-order__date">01.01.2015</span>
                </div>
                <div class="personal-order__cell">
                    <div class="personal-order__name ellipsis">Сетевой фильтр ЭРА 5гн+2xUSB, 2м, SFU-5es-2m-W sdfafsdkfjga sfakjfgafassjgas fkjag</div>
                    <span class="personal-order__info warning">Требуется предоплата</span>
                </div>
                <div class="personal-order__cell">
                    <span class="personal-order__deliv-type">Самовывоз 18.06.2015</span>
                    <div class="personal-order__deliv-info ellipsis">Постамат PickPoint<br>ул. Братиславская д. 14 sdlfkjahfasldkjahsdalskjhljksag lkgasdl lajdg sldjg</div>
                </div>
                <div class="personal-order__cell personal-order__price">
                    550 <span class="rubl">p</span>
                </div>
                <div class="personal-order__cell">
                    <span class="personal-order__status">Подтвержден</span>
                    <span class="personal-order__pay-status online">Оплатить онлайн</span>
                </div>
                <div class="personal-order__cell">
                    <span class="personal-order__more">Еще
                        <div class="personal-order__cancel">Повторить заказ</div>
                    </span>
                </div>
            </div>
        </div>
        <div class="personal-order__block">
                <span class="personal-order__year-container">
                   <span class="personal-order__year"> 2014</span>
                </span><span class="personal-order__year-total">1 заказ</span>

            <div class="personal-order__item">
                <div class="personal-order__cell">
                    <span class="personal-order__num">COXF-767608</span>
                    <span class="personal-order__date">01.01.2015</span>
                </div>
                <div class="personal-order__cell">
                    <div class="personal-order__name ellipsis">Сетевой фильтр ЭРА 5гн+2xUSB, 2м, SFU-5es-2m-W sdfafsdkfjga sfakjfgafassjgas fkjag</div>
                    <span class="personal-order__info warning">Требуется предоплата</span>
                </div>
                <div class="personal-order__cell">
                    <span class="personal-order__deliv-type">Самовывоз 18.06.2015</span>
                    <div class="personal-order__deliv-info ellipsis">Постамат PickPoint<br>ул. Братиславская д. 14 sdlfkjahfasldkjahsdalskjhljksag lkgasdl lajdg sldjg</div>
                </div>
                <div class="personal-order__cell personal-order__price">
                    550 <span class="rubl">p</span>
                </div>
                <div class="personal-order__cell">
                    <span class="personal-order__status">Подтвержден</span>
                    <span class="personal-order__pay-status online">Оплатить онлайн</span>
                </div>
                <div class="personal-order__cell">
                    <span class="personal-order__more">Еще
                        <div class="personal-order__cancel">Повторить заказ</div>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <? endif ?>

</div>