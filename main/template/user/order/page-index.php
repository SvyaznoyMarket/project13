<?php
/**
 * @var $page                              \View\User\Order\IndexPage
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

<?
$currentYear = (int)(new \DateTime())->format('Y');

$recommendationsHtml = [
    $helper->render('product/__slider', [
        'type'      => 'personal',
        'products'  => [],
        'url'       => $page->url('recommended', [
            'types'  => ['personal'],
            'sender' => [
                'position' => 'Basket',
            ],
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

<div class="personal">
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
                                <div class="personal-order__name">
                                    <div class="ellipsis"><div><?= $product->getName() ?> <?= $orderProduct->getQuantity() ?> шт</div>
                                    </div>
                                </div>

                                <? if ($moreProductCount = (count($order->product) - 1)): ?>
                                    <div style="color: #868686">и еще <?= $moreProductCount . ' ' . $helper->numberChoice($moreProductCount, ['товар', 'товара', 'товаров']) ?></div>
                                <? endif ?>
                            <? endif ?>
                            <span class="personal-order__info warning">
                            <? if ($order->prepaidSum): ?>
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
                            <div class="personal-order__deliv-info">
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
                            <span class="personal-order__status"><?= $order->getStatusText() ?></span>
                            <? if (isset($onlinePaymentAvailableByNumberErp[$order->numberErp]) && $onlinePaymentAvailableByNumberErp[$order->numberErp]): ?>
                                <span class="personal-order__pay-status online">Оплатить онлайн</span>
                            <? endif ?>
                        </div>
                        <div class="personal-order__cell">
                            <? if (false): ?>
                            <span class="personal-order__more">Еще
                                <div class="personal-order__cancel">Повторить заказ</div>
                            </span>
                            <? endif ?>
                        </div>
                    </div>
                <? endforeach ?>
            </div>
        </div>

        <?= array_shift($recommendationsHtml) ?>
    <? endforeach ?>

    <? foreach ($recommendationsHtml as $recommendationHtml): ?>
        <?= $recommendationHtml ?>
    <? endforeach ?>

</div>
