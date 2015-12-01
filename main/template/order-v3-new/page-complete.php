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
    $showStatus = ('call-center' === \App::session()->get(\App::config()->order['channelSessionKey']));
    $showPaymentStatus = ('call-center' !== \App::session()->get(\App::config()->order['channelSessionKey']));
    $showPartner = ('call-center' === \App::session()->get(\App::config()->order['channelSessionKey']));
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
                $isOnlinePaymentPossible =
                    (
                        !isset($onlinePaymentStatusByNumber[$order->number])
                        || (true === $onlinePaymentStatusByNumber[$order->number])
                    )
                    && ('call-center' !== \App::session()->get(\App::config()->order['channelSessionKey']))
                    && !$order->isPaid()
                    && !$order->isCredit()
                    && !$order->isPaidBySvyaznoy()
                ;

                $sumContainerId = sprintf('id-onlineDiscountSum-container', $order->id);

                // SITE-6304
                $checkedPaymentMethodId = $order->paymentId;
                if (!array_key_exists($order->paymentId, $onlinePaymentMethods) && ($paymentMethod = reset($onlinePaymentMethods) ?: null)) {
                    /** @var \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity $paymentMethod */
                    $checkedPaymentMethodId = $paymentMethod->id;
                }
            ?>

                <div class="orderLn table <? if ($showStatus): ?>orderLn--status<? endif ?>" data-order-id="<?= $order->getId() ?>" data-order-number="<?= $order->getNumber() ?>" data-order-number-erp="<?= $order->getNumberErp() ?>">
                    <div class="orderLn_l orderLn_cell">
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

                    <div class="orderLn_c orderLn_cell">
                        <div class="delivery-block">
                            <div class="delivery-block__type"><?= \RepositoryManager::deliveryType()->getEntityById($order->deliveryTypeId)->getShortName() ?>:</div>
                            <div class="delivery-block__info">
                                <? if ($order->deliveredAt) : ?>
                                <?
                                    $deliveryText =
                                        !empty($order->deliveryDateInterval['name'])
                                        ? $order->deliveryDateInterval['name']
                                        : (
                                            strftime('%e %b %Y', $order->deliveredAt->getTimestamp())
                                            ? $order->getDeliveredAt()->format('d.m.Y')
                                            : null
                                        )
                                    ;
                                ?>
                                    <?= $deliveryText ?>

                                <? endif ?>
                                <? if ($order->interval) : ?><?= $order->interval->getStart()?>…<?= $order->interval->getEnd() ?><? endif ?>
                            </div>
                        </div>
                    </div>

                    <? endif ?>

                    <? if ($showPaymentStatus): ?>
                    <!-- тип оплаты -->
                    <div class="payment-block orderLn_c orderLn_cell" style="width: 255px;">
                        <? if ($checkedPaymentMethod): ?>
                        <div class="payment-block__type">Способ оплаты: </div>
                        <div class="payment-block__logo">
                            <?= $checkedPaymentMethod->name ?>
                            <? if ($image = $checkedPaymentMethod->icon): ?>
                                <img src="<?= $image ?>" alt="<?= $helper->escape($checkedPaymentMethod->name) ?>" />
                            <? endif ?>
                        </div>
                        <? endif ?>
                    </div>
                    <? endif ?>

                    <!-- тип оплаты -->

                    <div class="orderLn_cell js-order-cell">
                        <? if ($order->paySum): ?>
                            <div class="order-sum">
                                <? if ($order->sum > $order->paySum): ?>
                                    <div class="order-sum__prev"><span class="line-through"><?= $helper->formatPrice($order->sum) ?></span> <span class="rubl">p</span></div>
                                <? endif ?>
                                <div class="order-sum__val"><?= $helper->formatPrice($order->paySum) ?> <span class="rubl">p</span></div>

                                <? if (isset($paymentEntity->methods[\Model\PaymentMethod\PaymentMethod\PaymentMethodEntity::PAYMENT_CREDIT]) && $order->isCredit()): ?>
                                    <button class="orderPayment_btn btn3 js-payment-popup-show">Оформить кредит</button>

                                    <div style="display: none;" class="payments-popup js-payment-popup">
                                        <div class="js-payment-popup-closer payments-popup__closer"></div>

                                        <?= $helper->render('order-v3-new/complete-blocks/_credit', [
                                            'order'      => $order,
                                            'banks'      => $banks,
                                            'creditData' => $creditData,
                                            'isStatic'   => false,
                                        ]) ?>
                                    </div>
                                <? endif ?>

                                <?
                                    $containerId = sprintf('id-order-%s-paymentMethod-container', $order->id);
                                ?>
                                <? if ($isOnlinePaymentPossible && (PaymentMethodEntity::PAYMENT_CASH === $order->paymentId)): ?>
                                    <div style="text-align: right;"><button class="orderPayment_btn btn3 js-payment-popup-show">Оплатить онлайн</button></div>

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
                                            Оплатить онлайн со скидкой
                                        </div>
                                        <div class="order-payment__sum-msg">
                                        <?
                                            $sum = ($checkedPaymentMethodId && $paymentEntity) ? ($paymentEntity->getPaymentSumByMethodId($checkedPaymentMethodId)) : null;
                                            if (empty($sum)) {
                                                $sum = $order->paySum;
                                            }
                                        ?>
                                            К оплате <span class="order-payment__sum"><span class="<?= $sumContainerId ?>"><?= $helper->formatPrice($sum) ?></span> <span class="rubl">p</span></span>
                                        </div>

                                        <? foreach ((new \View\Partial\PaymentMethods())->execute($helper, $onlinePaymentMethods, $checkedPaymentMethodId)['paymentMethodGroups'] as $paymentMethodGroup): ?>
                                            <ul class="payment-methods__lst <? if ($paymentMethodGroup['discount']): ?>payment-methods__lst_discount<? endif ?>">
                                                <? foreach ($paymentMethodGroup['paymentMethodGroups'] as $paymentMethodGroup2): ?>
                                                    <? if (count($paymentMethodGroup2['paymentMethods']) == 1): ?>
                                                        <?
                                                        $elementId = sprintf('order_%s-paymentMethod_%s', $order->id, $paymentMethodGroup2['paymentMethods'][0]['id']);
                                                        $name = sprintf('paymentMethodId_%s', $order->id);
                                                        ?>

                                                        <li class="payment-methods__i">
                                                            <input
                                                                id="<?= $elementId ?>"
                                                                type="radio"
                                                                name="<?= $name ?>"
                                                                value="<?= $paymentMethodGroup2['paymentMethods'][0]['id'] ?>"
                                                                data-url="<?= $formUrl ?>"
                                                                data-value="<?= $helper->json([
                                                                    'action' => isset($paymentMethodGroup['discount']) ? $paymentMethodGroup['discount']['action'] : null,
                                                                    'method' => $paymentMethodGroup2['paymentMethods'][0]['id'],
                                                                    'order'  => $order->id,
                                                                    'number' => $order->number,
                                                                    'url'    => \App::router()->generate('orderV3.complete', ['context' => $order->context], true),
                                                                ]) ?>"
                                                                <? if ($sum = (empty($paymentMethodGroup['discount']['sum']) ? $order->paySum : $paymentMethodGroup['discount']['sum'])): ?>
                                                                    data-sum="<?= $helper->json([
                                                                        'value' => $helper->formatPrice($sum)
                                                                    ])?>"
                                                                <? endif ?>
                                                                data-relation="<?= $helper->json([
                                                                    'formContainer'     => '.' . $containerId,
                                                                    'sumContainer'      => '.' . $sumContainerId,
                                                                ]) ?>"
                                                                class="customInput customInput-defradio2 js-customInput js-order-onlinePaymentMethod"
                                                                <? if ($paymentMethodGroup2['paymentMethods'][0]['selected']): ?>
                                                                    checked="checked"
                                                                    data-checked="true"
                                                                <? endif ?>
                                                                />
                                                            <label for="<?= $elementId ?>" class="customLabel customLabel-defradio2<? if ($paymentMethodGroup2['paymentMethods'][0]['selected']): ?> mChecked<? endif ?>">
                                                                <?= $helper->escape($paymentMethodGroup2['paymentMethods'][0]['name']) ?>
                                                                <? if ($paymentMethodGroup2['paymentMethods'][0]['icon']): ?>
                                                                    <img class="payment-methods__img" src="<?= $helper->escape($paymentMethodGroup2['paymentMethods'][0]['icon']) ?>" alt="<?= $helper->escape($paymentMethodGroup2['paymentMethods'][0]['name']) ?>" />
                                                                <? endif ?>
                                                            </label>
                                                        </li>
                                                    <? endif ?>
                                                <? endforeach ?>
                                            </ul>

                                            <?= $helper->renderWithMustache('order-v3-new/paymentMethod/discount', ['discount' => $paymentMethodGroup['discount']]) ?>
                                        <? endforeach ?>

                                        <div class="payments-popup__pay <?= $containerId ?>"></div>
                                        <p class="orderPayment_msg_hint">Вы будете перенаправлены на сайт платежной системы.</p>
                                    </div>
                                    <!-- END popup оплаты -->
                                <? elseif ($checkedPaymentMethod && $paymentEntity->methods && $isOnlinePaymentPossible): ?>
                                    <!--<button class="orderPayment_btn btn3 js-payment-popup-show">Оплатить</button>-->
                                    <input
                                        type="hidden"
                                        name="<?= sprintf('paymentMethodId_%s', $order->id) ?>"
                                        value="<?= $checkedPaymentMethod->id ?>"
                                        data-url="<?= $formUrl ?>"
                                        data-value="<?= $helper->json([
                                            'action' => $checkedPaymentMethod->discount ? $checkedPaymentMethod->discount->action : null,
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
                                    <div style="text-align: right;" class="<?= $containerId ?>"></div>
                                <? endif ?>
                            </div>
                        <? endif ?>

                        <? if ($order->isPaid()) : ?>
                            <!-- Оплачено -->
                            <div class="orderLn_row orderLn_row-bg orderLn_row-bg-grey jsOrderPaid">
                                <img class="orderLn_row_imgpay" src="/styles/order/img/payment.png" alt="Оплачено" />
                            </div>
                        <? endif ?>
                    </div>

                    <? if ($showStatus): ?>
                    <div class="orderLn_status orderLn_cell">
                        <div class="orderLn_status-title">Статус:</div>
                        <? if ($order->status): ?>
                            <strong class="orderLn_status-new"><?= $order->status->name ?></strong>
                        <? else:?>
                            <strong>Не известен</strong>
                        <? endif ?>

                        <? if ($showPartner): ?>
                        <?
                            $isSordex = false;
                            foreach ($order->product as $orderProduct) {
                                /** @var \Model\Product\Entity|null $product */
                                $product = isset($products[$orderProduct->getId()]) ? $products[$orderProduct->getId()] : null;
                                if (!$product) continue;
                                if ($product->hasSordexPartner()) {
                                    $isSordex = true;
                                    break;
                                }                            }

                        ?>
                            <? if ($isSordex): ?><div class="orderLn_status-sordex">Заказ Sordex</div><? endif ?>
                        <? endif ?>
                    </div>
                    <? endif ?>
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
