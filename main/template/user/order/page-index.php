<?php
/**
 * @var $page                                \View\User\Order\IndexPage
 * @var $helper                              \Helper\TemplateHelper
 * @var $user                                \Session\User
 * @var $orderCount                          int
 * @var $ordersByYear                        array
 * @var $orders                              \Model\User\Order\Entity[]
 * @var $orderProduct                        \Model\Order\Product\Entity|null
 * @var $product                             \Model\Product\Entity|null
 * @var $productsById                        \Model\Product\Entity[]
 * @var $point                               \Model\Point\PointEntity
 * @var $pointsByUi                          \Model\Point\PointEntity[]
 * @var $onlinePaymentAvailableByNumberErp   bool[]
 * @var $paymentEntitiesByNumberErp          \Model\PaymentMethod\PaymentEntity[]
 * @var $viewedProducts                      \Model\Product\Entity[]
 */
?>

<?
$currentYear = (int)(new \DateTime())->format('Y');

$isRich = \App::abTest()->isRichRelRecommendations();
$recommendationsSender = [
    'name' => $isRich ? 'rich' : 'retailrocket'
];

$recommendationsHtml = [
    $helper->render('product/__slider', [
        'type'      => $isRich ? 'personal_page.top' : 'personal',
        'products'  => [],
        'url'       => $page->url('recommended', [
            'types'  => $isRich ? ['personal_page.top'] : ['personal'],
            'sender' => [
                'position' => 'Basket',
            ] + $recommendationsSender,
            'showLimit' => 6,
        ]),
    ]),
    $helper->render('product/__slider', [
        'type'      => 'viewed',
        'title'     => 'Вы смотрели',
        'products'  => $viewedProducts,
        'limit'     => \App::config()->product['itemsInSlider'],
        'page'      => 1,
        'class'     => 'slideItem-viewed',
        'isCompact' => true,
    ]),
];
?>

<div class="personal" id="personal-container">
    <?= $page->render('user/_menu', ['page' => $page]) ?>

    <? foreach ($ordersByYear as $year => $orders): ?>
    <?
        $containerId = 'id-orderContainer-' . $year;
        $count = count($orders);
        $isCurrentOrders = $currentYear === $year;
    ?>

        <div class="personal__orders <? if ($isCurrentOrders): ?>current<? endif ?>">
            <div class="personal__orders-head"><?= ($isCurrentOrders ? 'Текущие заказы' : 'История заказов') ?></div>
            <div class="<?= $containerId ?> personal-order__block <?= ($currentYear == $year ? 'expanded' : '') ?>">
                <? if ($currentYear !== $year): ?>
                    <div class="js-orderContainer-link personal-order__year-link" data-relation="<?= $helper->json(['container' => '.' . $containerId]) ?>">
                        <span class="personal-order__year-container">
                            <span class="personal-order__year "> <?= $year ?></span>
                        </span>
                        <span class="personal-order__year-total"><?= ($count . ' ' . $helper->numberChoice($count, ['заказ', 'заказа', 'заказов'])) ?></span>
                    </div>
                <? endif ?>

                <? foreach ($orders as $order): ?>
                <?
                    $paymentContainerId = sprintf('order-paymentContainer-%s', md5($order->id . '-' . $order->numberErp));
                ?>
                    <div class="personal-order__item">

                        <? if ($order->isCancelRequestAvailable): ?>
                        <div class="personal-order__toggler">
                            <span class="personal-order__toggler-txt">Еще</span>
                            <div class="personal-order__toggler-popup">
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
                        <a class="personal-order__item-link" href="<?= $page->url('user.order', ['orderId' => $order->id ]) ?>">
                            <div class="personal-order__cell">
                                <span class="personal-order__num"><?= $order->numberErp ?></span>
                                <span class="personal-order__date"><?= $order->createdAt ? $order->createdAt->format('d.m.Y') : '' ?></span>
                            </div>
                            <div class="personal-order__cell">
                                <? if (($orderProduct = reset($order->product) ?: null) && ($product = @$productsById[$orderProduct->getId()] ?: null)): ?>
                                    <div class="personal-order__name">
                                        <div class="ellipsis"><div><?= $product->getName() ?> <?= $orderProduct->getQuantity() ?> шт</div>
                                        </div>
                                    </div>

                                    <? if ($moreProductCount = (count($order->product) - 1)): ?>
                                        <div style="color: #868686">и еще <?= $moreProductCount . ' ' . $helper->numberChoice($moreProductCount, ['товар', 'товара', 'товаров']) ?></div>
                                    <? endif ?>
                                <? endif ?>
                                <span class="personal-order__info warning">
                            <? if (!$order->isPaid() && $order->prepaidSum): ?>Требуется предоплата<? endif ?>
                            </span>
                            </div>
                            <div class="personal-order__cell">
                            <span class="personal-order__deliv-type">
                                <?= $order->getDeliveryTypeName() ?>
                                <? if ($order->getDelivery() && ($deliveredAt = $order->getDelivery()->getDeliveredAt())): ?>
                                    <?= $deliveredAt->format('d.m.Y') ?>
                                <? endif ?>
                            </span>
                                <div class="personal-order__deliv-info">
                                    <? if ($order->pointUi && ($point = $pointsByUi[$order->pointUi])): ?>
                                        <?= $point->getTypeName() ?><br><?= $point->address ?>
                                    <? endif ?>
                                    <? if ($order->getDelivery()->isShipping) : ?>
                                        <?= $order->address ?>
                                    <? endif ?>
                                </div>
                            </div>
                        </a>
                            <div class="personal-order__cell personal-order__price">
                                <?= $helper->formatPrice($order->totalPaySum) ?> <span class="rubl">p</span>
                            </div>
                            <div class="personal-order__cell">
                                <? if ($status = $order->status): ?>
                                    <span class="personal-order__status"><?= $status->name ?></span>
                                <? endif ?>

                                <? if ($paymentStatus = $order->paymentStatus): ?>
                                    <span class="personal-order__status"><?= $paymentStatus->name ?></span>
                                <? endif ?>

                                <? if (isset($onlinePaymentAvailableByNumberErp[$order->numberErp]) && $onlinePaymentAvailableByNumberErp[$order->numberErp]): ?>
                                    <a
                                        href="#"
                                        class="js-payment-popup-show personal-order__pay-status online"
                                        data-relation="<?= $helper->json(['container' => '.' . $paymentContainerId]) ?>"
                                    >Оплатить онлайн</a>
                                <? endif ?>
                            </div>


                    </div>

                    <? if (!empty($paymentEntitiesByNumberErp[$order->numberErp])): ?>
                    <div class="<?= $paymentContainerId ?>">
                        <?= $helper->render('user/order/__onlinePayment-popup', ['order' => $order, 'paymentEntity' => $paymentEntitiesByNumberErp[$order->numberErp]]) ?>
                    </div>
                    <? endif ?>
                <? endforeach ?>
            </div>
        </div>

        <?= array_shift($recommendationsHtml) ?>
    <? endforeach ?>

    <? foreach ($recommendationsHtml as $recommendationHtml): ?>
        <?= $recommendationHtml ?>
    <? endforeach ?>

    <script id="tpl-user-deleteOrderPopup" type="text/html" data-partial="<?= $helper->json([]) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/user/order/_deleteOrder-popup.mustache') ?>
    </script>

</div>
