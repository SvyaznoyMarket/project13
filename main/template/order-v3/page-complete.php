<?php

return function(
    \Helper\TemplateHelper $helper,
    $orders,
    $ordersPayment,
    $products,
    $userEntity,
    $sessionIsReaded
) {
/** @var $products \Model\Product\Entity[] */
    $page = new \View\OrderV3\CompletePage();
?>
<style>
    .jsPaymentForms {
        display: none;
    }
</style>
<?= $helper->render('order-v3/__head', ['step' => 3]) ?>

    <section class="orderCnt jsOrderV3PageComplete">
        <h1 class="orderCnt_t">Ваши заказы</h1>

        <div class="orderLnSet">

            <? foreach ($orders as $order): ?>
            <? /** @var $order \Model\Order\Entity */?>

                <div class="orderLn clearfix" data-order-id="<?= $order->getId() ?>" data-order-number="<?= $order->getNumber() ?>">
                    <div class="orderLn_l">

                        <? if ($userEntity) : ?>
                            <div class="orderLn_row orderLn_row-t"><strong>Заказ</strong> <a href="<?= \App::router()->generate('user.order', ['orderId' =>$order->getId()]) ?>"><?= $order->getNumberErp()?></a></div>
                        <? else : ?>
                            <div class="orderLn_row orderLn_row-t"><strong>Заказ</strong> <?= $order->getNumberErp()?></div>
                        <? endif; ?>

                        <ul class="orderLn_lst">
                            <? foreach ($order->getProduct() as $key => $product): ?>
                            <? /** @var $product \Model\Order\Product\Entity */?>
                                <? if (isset($products[$product->getId()])) : ?>
                                    <li class="orderLn_lst_i"><?= $products[$product->getId()]->getWebName() == '' ? $products[$product->getId()]->getName():  $products[$product->getId()]->getWebName(); ?> <?= $product->getQuantity() ?> шт.</li>
                                <? endif ?>
                                <? if ($key == 2) : ?>
                                    <? $orderProductsString = $helper->numberChoiceWithCount(count($order->getProduct()) - 2, ['товар', 'товара', 'товаров']) ?>
                                    <? if ($userEntity) : ?>
                                        <li class="orderLn_lst_i"><a class="orderLn_lst_lk" href="<?= \App::router()->generate('user.order', ['orderId' =>$order->getId()]) ?>">и ещё <?= $orderProductsString ?></a></li>
                                    <? else : ?>
                                        <li class="orderLn_lst_i">и ещё <?= $orderProductsString ?></li>
                                    <? endif; ?>
                                <? continue; endif; ?>
                            <? endforeach ?>
                        </ul>

                    </div>

                    <? if (\RepositoryManager::deliveryType()->getEntityById($order->deliveryTypeId)) : ?>

                    <div class="orderLn_c">
                        <div><?= \RepositoryManager::deliveryType()->getEntityById($order->deliveryTypeId)->getShortName() ?>
                            <? if ($order->deliveredAt) : ?><?= strftime('%e %b %Y', $order->deliveredAt->getTimestamp()) ?><? endif; ?>
                            <? if ($order->interval) : ?><?= $order->interval->getStart()?>…<?= $order->interval->getEnd() ?><? endif; ?>
                        </div>
                        <!--<div>Оплата при получении: наличные, банковская карта</div>-->
                    </div>

                    <? endif; ?>

                    <div class="orderLn_r">
                        <div class="orderLn_row orderLn_row-summ">
                            <span class="summT">Сумма заказа:</span>
                            <span class="summP"><?= $helper->formatPrice($order->getSum()) ?> <span class="rubl">p</span></span>
                        </div>

                        <? if (isset($ordersPayment[$order->getNumber()])) : ?>
                        <? $paymentEntity = $ordersPayment[$order->getNumber()]; /** @var $paymentEntity \Model\PaymentMethod\PaymentEntity */?>

                            <? if (isset($paymentEntity->groups[2])) : ?>

                            <div class="orderLn_row orderLn_row-bg">

                                <? if (isset($paymentEntity->methods[\Model\PaymentMethod\PaymentMethod\PaymentMethodEntity::PAYMENT_CREDIT])
                                        && isset($order->meta_data['preferred_payment_id'])
                                        && reset($order->meta_data['preferred_payment_id']) == \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity::PAYMENT_CREDIT) : ?>

                                    <!-- Кредит -->

                                    <div class="payT">Покупка в кредит</div>
                                    <a href="" class="btnLightGrey"><strong>Заполнить заявку</strong></a>

                                <? elseif (isset($paymentEntity->groups[\Model\PaymentMethod\PaymentGroup\PaymentGroupEntity::PAYMENT_NOW])) : ?>
                                <? $paymentMethods = array_filter($paymentEntity->methods, function (\Model\PaymentMethod\PaymentMethod\PaymentMethodEntity $method) use ($paymentEntity) {return $method->paymentGroup === $paymentEntity->groups[\Model\PaymentMethod\PaymentGroup\PaymentGroupEntity::PAYMENT_NOW]; }) ?>

                                    <!-- Онлайн-оплата -->

                                    <? if ($order->sum > \App::config()->order['prepayment']['priceLimit']) : ?>

                                        <div class="payT">Требуется предоплата</div>
                                        <div class="orderLn_box">
                                            <a href="" class="orderLn_btn btnLightGrey">
                                                <? foreach ($paymentMethods as $method) : ?>
                                                    <img src="<?= $method->icon; ?>" alt="" />
                                                <? endforeach; ?>
                                            </a>
                                            <ul style="display: none;" class="customSel_lst popupFl">
                                                <? foreach ($paymentMethods as $method) : ?>
                                                    <li class="customSel_i jsPaymentMethod">
                                                        <strong><?= $method->name; ?></strong><br/>
                                                        <?= $method->description; ?>
                                                    </li>
                                                <? endforeach; ?>
                                            </ul>
                                        </div>

                                    <? else : ?>

                                        <div class="payT">Можно <span class="payBtn btn4 jsOnlinePaymentSpan"><span class="brb-dt">оплатить онлайн</span></span></div>

                                        <? foreach ($paymentMethods as $method) : ?>
                                            <img src="<?= $method->icon; ?>" alt="" />
                                        <? endforeach; ?>

                                        <ul style="display: none;" class="customSel_lst popupFl customSel_lst-pay jsOnlinePaymentList">
                                        <? foreach ($paymentMethods as $method) : ?>
                                            <li class="customSel_i jsPaymentMethod" data-value="<?= $method->id; ?>">
                                                <strong><?= $method->name; ?></strong><br/>
                                                <?= $method->description; ?>
                                            </li>
                                        <? endforeach; ?>
                                        </ul>

                                    <? endif; ?>

                                <? endif; ?>

                            </div>

                            <? endif; ?>

                        <? endif; ?>
                    </div>
                </div>

            <? endforeach; ?>

        </div>

        <div class="orderCompl clearfix">
            <button class="orderCompl_btn m-auto btnsubmit" onclick="window.location.href='<?= $helper->url('homepage') ?>'">Продолжить покупки</button>
        </div>
    </section>

    <? if (!$sessionIsReaded) {
        // Если сесиия уже была прочитана, значит юзер обновляет страницу, не трекаем партнёров вторично
        echo $page->render('order/_analytics', array(
            'orders'       => $orders,
            'productsById' => $products,
        ));

        echo $page->render('order/partner-counter/_complete', [
            'orders'       => $orders,
            'productsById' => $products,
        ]);

        echo $helper->render('order/__analyticsData', ['orders' => $orders, 'productsById' => $products]);

        // Flocktory popup
        echo $helper->render('order-v3/partner-counter/_flocktory-complete',[
            'orders'    => $orders,
            'products'  => $products,
        ]);
    } ?>

<? };



