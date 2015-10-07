<?php

use \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity, \Model\PaymentMethod\PaymentGroup\PaymentGroupEntity;

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\Order\Entity[] $orders
 * @param \Model\PaymentMethod\PaymentEntity $ordersPayment
 * @param \Model\Product\Entity[] $products
 * @param $userEntity
 * @param $sessionIsReaded
 * @param $banks
 * @param $creditData
 * @param $subscribe
 * @param bool[] $onlinePaymentStatusByNumber
 */
$f = function(
    \Helper\TemplateHelper $helper,
    $orders,
    $ordersPayment,
    $products,
    $userEntity,
    $sessionIsReaded,
    $banks,
    $creditData,
    $subscribe,
    $onlinePaymentStatusByNumber = []
) {
    $page = new \View\OrderV3\CompletePage();
    array_map(function(\Model\PaymentMethod\PaymentEntity &$entity) {$entity->unsetSvyaznoyClub();}, $ordersPayment); // fix for SITE-5229 (see comments)

    $formUrl = \App::router()->generate('orderV3.paymentForm');
?>
    <div class="order__wrap">
    <section class="orderCnt jsOrderV3PageComplete order-page">
        <div class="pagehead"><h1 class="orderCnt_t">Ваши заказы</h1></div>

        <div class="orderLnSet">

            <? foreach ($orders as $order): ?>
            <?
                /** @var \Model\PaymentMethod\PaymentEntity|null $paymentEntity */
                $paymentEntity = isset($ordersPayment[$order->getNumber()]) ? $ordersPayment[$order->getNumber()] : null;
                /** @var \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity|null $checkedPaymentMethod */
                $checkedPaymentMethod = null;
                foreach ($paymentEntity->methods as $iPaymentMethod) {
                    if ($iPaymentMethod->id == $order->paymentId) {
                        $checkedPaymentMethod = $iPaymentMethod;
                        break;
                    }
                }

                /** @var \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity[] $paymentMethods */
                $onlinePaymentMethods = array_filter($paymentEntity->methods, function(\Model\PaymentMethod\PaymentMethod\PaymentMethodEntity $paymentMethod) {
                    return $paymentMethod->isOnline;
                });
                $paymentMethodsByDiscount = [];
                foreach ($onlinePaymentMethods as $iPaymentMethod) {
                    $index = $iPaymentMethod->discount ? 0 : 1;
                    $paymentMethodsByDiscount[$index][] = $iPaymentMethod;
                }
                ksort($paymentMethodsByDiscount);
                $isOnlinePaymentPossible =
                    (
                        !isset($onlinePaymentStatusByNumber[$order->number])
                        || (true === $onlinePaymentStatusByNumber[$order->number])
                    )
                    && ((bool)$paymentEntity ? array_key_exists(PaymentGroupEntity::PAYMENT_NOW, $paymentEntity->groups) : false)
                ;

                $discountContainerId = sprintf('id-onlineDiscount-container', $order->id);
            ?>

                <div class="orderLn clearfix" data-order-id="<?= $order->getId() ?>" data-order-number="<?= $order->getNumber() ?>" data-order-number-erp="<?= $order->getNumberErp() ?>">
                    <div class="orderLn_l">

                        <? if ($userEntity) : ?>
                            <div class="orderLn_row orderLn_row-t"><strong>Заказ</strong> <a href="<?= \App::router()->generate('user.order', ['orderId' =>$order->getId()]) ?>"><?= $order->getNumberErp()?></a></div>
                        <? else : ?>
                            <div class="orderLn_row orderLn_row-t"><strong>Заказ</strong> <?= $order->getNumberErp()?></div>
                        <? endif ?>

                        <ul class="orderLn_lst">
                            <? foreach ($order->getProduct() as $key => $product): ?>
                            <? /** @var $product \Model\Order\Product\Entity */?>
                                <? if (isset($products[$product->getId()])) : ?>
                                    <li class="orderLn_lst_i"><?= $products[$product->getId()]->getPrefix() == '' ? mb_strimwidth($products[$product->getId()]->getName(), 0, 40, '…') :  mb_strimwidth($products[$product->getId()]->getPrefix(), 0, 40, '…') ?> <?= $product->getQuantity() ?> шт.</li>
                                <? endif ?>
                                <? if ($key == 2 && count($order->getProduct()) > 3) : ?>
                                    <? $orderProductsString = $helper->numberChoiceWithCount(count($order->getProduct()) - 3, ['товар', 'товара', 'товаров']) ?>
                                    <? if ($userEntity) : ?>
                                        <li class="orderLn_lst_i"><a class="orderLn_lst_lk" href="<?= \App::router()->generate('user.order', ['orderId' =>$order->getId()]) ?>">и ещё <?= $orderProductsString ?></a></li>
                                    <? else : ?>
                                        <li class="orderLn_lst_i">и ещё <?= $orderProductsString ?></li>
                                    <? endif ?>
                                <? break; endif ?>
                            <? endforeach ?>
                        </ul>

                    </div>

                    <? if (\RepositoryManager::deliveryType()->getEntityById($order->deliveryTypeId)) : ?>

                    <div class="orderLn_c">
                        <div class="delivery-block">
                            <div class="delivery-block__type"><?= \RepositoryManager::deliveryType()->getEntityById($order->deliveryTypeId)->getShortName() ?></div>
                            <div class="delivery-block__info"><? if ($order->deliveredAt) : ?><?= strftime('%e %b %Y', $order->deliveredAt->getTimestamp()) ?><? endif ?>
                                <? if ($order->interval) : ?><?= $order->interval->getStart()?>…<?= $order->interval->getEnd() ?><? endif ?>
                            </div>
                        </div>
                        <!--<div>Оплата при получении: наличные, банковская карта</div>-->
                    </div>

                    <? endif ?>
                    <!-- тип оплаты -->
                    <div class="payment-block">
                        <? if ($checkedPaymentMethod): ?>
                        <div class="payment-block__type">Тип оплаты: </div>
                        <div class="payment-block__logo">
                            <?= $checkedPaymentMethod->name ?>
                            <? if ($image = $checkedPaymentMethod->icon): ?>
                                <img src="<?= $image ?>" alt="<?= $helper->escape($checkedPaymentMethod->name) ?>" />
                            <? endif ?>
                        </div>
                        <? endif ?>
                    </div>
                    <!-- тип оплаты -->

                    <div class="orderLn_r">
                        <? if ($order->getPaySum()): ?>
                            <div class="order-sum">
                                <? if (false): ?>
                                    <div class="order-sum__prev"><span class="line-through"><?= $helper->formatPrice($order->getPaySum()) ?></span> <span class="rubl">p</span></div>
                                <? endif ?>
                                <div class="order-sum__val"><?= $helper->formatPrice($order->getPaySum()) ?> <span class="rubl">p</span></div>
                                <? if (PaymentMethodEntity::PAYMENT_CASH === $order->paymentId): ?>
                                    <button class="orderPayment_btn btn3 js-payment-popup-show">Оплатить онлайн</button>

                                    <ul class="payments__lst">
                                        <? foreach ($paymentEntity->methods as $paymentMethod): ?>
                                        <?
                                            $image = $paymentMethod->icon ? str_replace('.png', '-g.png', $paymentMethod->icon) : null;
                                            if (!$image) continue;
                                        ?>
                                            <li class="payments__i"><img src="<?= $image ?>" alt="<?= $helper->escape($paymentMethod->name) ?>" /></li>
                                        <? endforeach ?>
                                    </ul>

                                    <!-- popup оплаты -->
                                    <div class="payments-popup js-payment-popup">
                                        <div class="js-payment-popup-closer payments-popup__closer"></div>

                                        <div class="orderPayment_msg_head">
                                            Онлайн-оплата
                                        </div>
                                        <div class="order-payment__sum-msg">
                                            К оплате <span class="order-payment__sum"><?= $helper->formatPrice($order->getPaySum()) ?> <span class="rubl">p</span></span>
                                        </div>

                                        <!-- Новые способы оплаты - статика -->
                                        <div class="payment-methods__discount discount">
                                            <div class="<?= $discountContainerId ?>">
                                                <span class="discount__val">Скидка 15%</span>
                                            </div>
                                        </div>
                                        <ul class="payment-methods__lst">
                                        <? foreach ($paymentMethodsByDiscount as $paymentMethodChunk): ?>
                                            <? foreach ($paymentMethodChunk as $paymentMethod): ?>
                                            <?
                                                $containerId = sprintf('id-order-%s-paymentMethod-container', $order->id);
                                                $elementId = sprintf('order-%s-paymentMethod-%s', $order->id, $paymentMethod->id);
                                                $checked = $order->paymentId == $paymentMethod->id;
                                            ?>
                                                <li class="payment-methods__i">
                                                    <input
                                                        id="<?= $elementId ?>"
                                                        type="radio"
                                                        name="<?= sprintf('paymentMethodId_%s', $order->id) ?>"
                                                        value="<?= $paymentMethod->id ?>"
                                                        data-url="<?= $formUrl ?>"
                                                        data-value="<?= $helper->json([
                                                            'method' => $paymentMethod->id,
                                                            'order'  => $order->id,
                                                            'number' => $order->number,
                                                            'url'    => \App::router()->generate('orderV3.complete', ['context' => $order->context], true),
                                                        ]) ?>"
                                                        <? if ($paymentMethod->isOnline): ?>
                                                            data-discount="true"
                                                        <? endif ?>
                                                        data-relation="<?= $helper->json([
                                                            'formContainer'     => '.' . $containerId,
                                                            'discountContainer' => '.' . $discountContainerId,
                                                        ]) ?>"
                                                        class="customInput customInput-defradio2 js-customInput js-order-onlinePaymentMethod"
                                                        <? if ($checked): ?> checked="checked"<? endif ?>
                                                    />
                                                    <label for="<?= $elementId ?>" class="customLabel customLabel-defradio2<? if ($checked): ?> mChecked<? endif ?>">
                                                        <?= $paymentMethod->name ?>
                                                        <? if ($image = $paymentMethod->icon): ?>
                                                            <img class="payment-methods__img" src="<?= $image ?>" alt="<?= $helper->escape($paymentMethod->name) ?>" />
                                                        <? endif ?>
                                                    </label>
                                                </li>
                                            <? endforeach ?>
                                            <li class="payment-methods__i"><br /></li>
                                        <? endforeach ?>
                                        </ul>
                                        <!-- END Новые способы оплаты - статика -->
                                        <div class="payments-popup__pay <?= $containerId ?>"></div>
                                        <p class="orderPayment_msg_hint">Вы будете перенаправлены на сайт платежной системы.</p>
                                    </div>
                                    <!-- END popup оплаты -->
                                <? elseif ($checkedPaymentMethod && $paymentEntity->methods && $isOnlinePaymentPossible): ?>
                                <?
                                    $containerId = sprintf('id-order-%s-paymentMethod-container', $order->id);
                                ?>
                                    <!--<button class="orderPayment_btn btn3 js-payment-popup-show">Оплатить</button>-->
                                    <input
                                        type="hidden"
                                        name="<?= sprintf('paymentMethodId_%s', $order->id) ?>"
                                        value="<?= $checkedPaymentMethod->id ?>"
                                        data-url="<?= $formUrl ?>"
                                        data-value="<?= $helper->json([
                                            'method' => $checkedPaymentMethod->id,
                                            'order'  => $order->id,
                                            'number' => $order->number,
                                            'url'    => \App::router()->generate('orderV3.complete', ['context' => $order->context], true),
                                        ]) ?>"
                                        data-relation="<?= $helper->json([
                                            'formContainer' => '.' . $containerId,
                                        ]) ?>"
                                        data-checked="true"
                                        class="js-order-onlinePaymentMethod"
                                    />
                                    <div class="<?= $containerId ?>"></div>
                                <? endif ?>
                            </div>
                        <? endif ?>

                        <? if ($order->isPaid()) : ?>
                            <!-- Оплачено -->
                            <div class="orderLn_row orderLn_row-bg orderLn_row-bg-grey jsOrderPaid">
                                <img class="orderLn_row_imgpay" src="/styles/order/img/payment.png" alt="" />
                            </div>
                        <? endif ?>
                    </div>
                </div>

            <? endforeach ?>

        </div>

        <div class="orderCompl orderCompl_final clearfix">
            <a class="orderCompl_continue_link" href="<?= $helper->url('homepage') ?>">Вернуться на главную</a>
        </div>
    </section>
    </div>
    <? if (!$sessionIsReaded): ?>
        <span class="js-orderV3New-complete-subscribe" data-value="<?=$helper->json(['subscribe' => $subscribe, 'email' => isset($orders[0]->email) ? $orders[0]->email : null])?>"></span>

        <?
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

        echo $helper->render('order/__saleAnalytics', ['orders' => $orders]);

        // Flocktory popup
        echo $helper->render('order-v3/partner-counter/_flocktory-complete',[
            'orders'    => $orders,
            'products'  => $products,
        ]);
        ?>
    <? endif ?>

<? }; return $f;