<?php
/**
 * @var $page               \View\User\OrderPage
 * @var $user               \Session\User
 * @var $deliveryTypesById  \Model\DeliveryType\Entity[]
 * @var $paymentMethodsById \Model\PaymentMethod\Entity[]
 * @var $orders             \Model\Order\Entity[]
 * @var $productsById       \Model\Product\CartEntity[]
 * @var $servicesById       \Model\Product\Service\Entity[]
 * @var $delivery           \Model\Order\Delivery\Entity
 */
?>

<div class="fl"><strong class="font16">Мои заказы (<?= count($orders) ?>)</strong></div>
<div class="clear pb20"></div>

<? if (count($orders) < 1): ?>
    <div>У Вас пока нет ни одного заказа.</div>
<? endif ?>

<? foreach ($orders as $order): ?>
    <?
    $deliveries = $order->getDelivery();
    $delivery = reset($deliveries);
    $deliveryType = ($delivery && isset($deliveryTypesById[$delivery->getTypeId()])) ? $deliveryTypesById[$delivery->getTypeId()] : null;

    $paymentMethod = ($order->getPaymentId() && isset($paymentMethodsById[$order->getPaymentId()])) ? $paymentMethodsById[$order->getPaymentId()] : null;
    ?>

    <? if (\Model\Order\Entity::STATUS_READY == $order->getStatusId()): ?>
        <div class="fr font16 orange pb10">Заказ выполнен</div>
    <? elseif (\Model\Order\Entity::STATUS_CANCELED == $order->getStatusId()): ?>
        <div class="fr font16 red pb10">заказ отменен</div>
    <? endif ?>

    <div class="font16 orange pb10">
        <strong>
            Заказ № <?= $order->getNumber() ?>
        </strong>
        <? if ($order->getCreatedAt()): ?>
            от <?= $order->getCreatedAt()->format('d.m.Y') ?>
        <? endif ?>


        <!--на сумму&nbsp;<?= $page->helper->formatPrice($order->getSum()) ?> <span class="rubl">p</span>-->
    </div>

    <table class="order mb15">
        <? foreach ($order->getProduct() as $orderProduct): ?>
            <?
                if (empty($productsById[$orderProduct->getId()])) continue;
                $product = $productsById[$orderProduct->getId()];
            ?>
            <tr>
                <th>
                    <a href="<?= $product->getLink() ?>">
                        <?= $product->getName() ?>
                        <? if ($orderProduct->getQuantity()): ?>
                            (<?= $orderProduct->getQuantity() ?> шт.)
                        <? endif ?>
                    </a>
                </th>
                <td>
                    <strong class="font14"><?= $page->helper->formatPrice($orderProduct->getPrice()) ?>&nbsp;<span class="rubl">p</span></strong>
                </td>
            </tr>

            <? if ($orderProduct->getWarrantyId() && ($warranty = $product->getWarrantyById($orderProduct->getWarrantyId()))): ?>
            <tr>
                <th>
                    <?= $warranty->getName() ?>
                </th>
                <td>
                    <strong class="font14"><?= $orderProduct->getWarrantyPrice() ?>&nbsp;<span class="rubl">p</span></strong>
                </td>
            </tr>
            <? endif ?>
        <? endforeach ?>

        <? foreach ($order->getService() as $orderService): ?>
            <?
                if (empty($servicesById[$orderService->getId()])) continue;
                $service = $servicesById[$orderService->getId()];
            ?>
            <tr>
                <th>
                    <a href="<?= $page->url('service.show', array('serviceToken' => $service->getToken())) ?>">
                        <?= $service->getName() ?>
                        <? if ($orderService->getQuantity()): ?>
                            (<?= $orderService->getQuantity() ?> шт.)
                        <? endif ?>
                    </a>
                </th>
                <td>
                    <strong class="font14"><?= $page->helper->formatPrice($orderService->getPrice()) ?>&nbsp;<span class="rubl">p</span></strong>
                </td>
            </tr>
        <? endforeach ?>

        <? if ($deliveryType): ?>
            <tr>
                <th>
                    <?= $deliveryType ? $deliveryType->getName() : '' ?>
                </th>
                <td>
                <? if ($delivery->getPrice()): ?>
                    <strong class="font14"><?= $page->helper->formatPrice($delivery->getPrice()) ?>&nbsp;<span class="rubl">p</span></strong>
                <? endif ?>
                </td>
            </tr>
        <? endif ?>

        <tr>
            <th>
                <? if ($delivery && $delivery->getDeliveredAt()): ?>
                <div class="font12 pb5">
                    <?= $delivery->getDeliveredAt()->format('d.m.Y') ?>
                </div>
                <? endif ?>

                <? if ($paymentMethod): ?>
                <div class="font12">
                    <?= $paymentMethod->getName() ?>
                </div>
                <? endif ?>

                <? if ($user->getEntity() && $user->getEntity()->getIsCorporative()): ?>
                    <div class="font12">Счет:
                        <? if ($order->getBill()): ?>
                            <a href="<?= $page->url('order.bill', array('orderNumber' => $order->getNumber())) ?>">выставлен</a>
                        <? else: ?>
                            выставляется
                        <? endif ?>
                    </div>
                <? endif ?>
            </th>
            <td>
                <?= ($order->getPaymentStatusId() == \Model\Order\Entity::PAYMENT_STATUS_PAID) ? 'Оплачено' : 'Итого к оплате' ?>:<br><strong class="font18"><?= $page->helper->formatPrice($order->getSum()) ?>&nbsp;<span class="rubl">p</span></strong>
            </td>
        </tr>
    </table>
<? endforeach ?>