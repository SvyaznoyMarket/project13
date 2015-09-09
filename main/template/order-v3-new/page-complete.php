<?php

return function(
    \Helper\TemplateHelper $helper,
    $orders,
    $ordersPayment,
    $products,
    $userEntity,
    $sessionIsReaded,
    $banks,
    $creditData,
    $subscribe
) {
/** @var $products \Model\Product\Entity[] */
    $page = new \View\OrderV3\CompletePage();
    array_map(function(\Model\PaymentMethod\PaymentEntity &$entity) {$entity->unsetSvyaznoyClub();}, $ordersPayment); // fix for SITE-5229 (see comments)
?>
<style>
    .jsPaymentForms {
        display: none;
    }
</style>

    <section class="orderCnt jsOrderV3PageComplete">
        <h1 class="orderCnt_t">Ваши заказы</h1>

        <div class="orderLnSet">

            <? foreach ($orders as $order): ?>
            <? /** @var $order \Model\Order\Entity */?>

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
                    <!-- статика: тип оплаты -->
                    <div class="payment-block">
                        <div class="payment-block__type">Тип оплаты: </div>
                        <div class="payment-block__logo">
                            Яндекс.деньги <img src="/styles/order-new/img/payment/pay-yandex.png">
                            <!--При получении: наличные, банковская карта-->
                        </div>
                    </div>
                    <!-- END статика: тип оплаты -->

                    <div class="orderLn_r">
                        <? if ($order->getPaySum()): ?>
                            <div class="order-sum">
                                <div class="order-sum__prev"><span class="line-through">22 244</span> <span class="rubl">p</span></div>
                                <div class="order-sum__val"><?= $helper->formatPrice($order->getPaySum()) ?> <span class="rubl">p</span></div>
                                <button class="orderPayment_btn btn3 js-payment-popup-show">Оплатить онлайн</button>
                                <ul class="payments__lst">
                                    <li class="payments__i"><img src="/styles/order-new/img/payment/pay-card-g.png"></li>
                                    <li class="payments__i"><img src="/styles/order-new/img/payment/pay-yandex-g.png"></li>
                                    <li class="payments__i"><img src="/styles/order-new/img/payment/pay-webmoney-g.png"></li>
                                    <li class="payments__i"><img src="/styles/order-new/img/payment/pay-qiwi-g.png"></li>
                                    <li class="payments__i"><img src="/styles/order-new/img/payment/pay-psb-g.png"></li>
                                </ul>


                            <!-- popup оплаты -->
                            <div class="payments-popup js-payment-popup">
                                <div class="js-payment-popup-closer payments-popup__closer"></div>

                                <div class="orderPayment_msg_head">
                                    Онлайн-оплата
                                </div>
                                <div class="order-payment__sum-msg">
                                    К оплате <span class="order-payment__sum">1500 <span class="rubl">p</span></span>
                                </div>

                                <!-- Новые способы оплаты - статика -->
                                <div class="payment-methods__discount discount">
                                    <span class="discount__val">Скидка 15%</span>
                                </div>
                                <ul class="payment-methods__lst">
                                    <li class="payment-methods__i">
                                        <input id="payment-card" type="radio" name="payment-type[]" value="by_card" class="customInput customInput-defradio2 jsPaymentMethodRadio js-customInput" checked="">
                                        <label for="payment-card" class="customLabel customLabel-defradio2 mChecked">
                                            Банковская карта
                                            <img class="payment-methods__img" src="/styles/order-new/img/payment/pay-card.png">
                                        </label>
                                    </li>
                                    <li class="payment-methods__i">
                                        <input id="payment-yandex" type="radio" name="payment-type[]" value="by_yandex" class="customInput customInput-defradio2 jsPaymentMethodRadio js-customInput" >
                                        <label for="payment-yandex" class="customLabel customLabel-defradio2">
                                            Яндекс.Деньги
                                            <img class="payment-methods__img" src="/styles/order-new/img/payment/pay-yandex.png">
                                        </label>
                                    </li>
                                    <li class="payment-methods__i">
                                        <input id="payment-webmoney" type="radio" name="payment-type[]" value="by_webmoney" class="customInput customInput-defradio2 jsPaymentMethodRadio js-customInput" >
                                        <label for="payment-webmoney" class="customLabel customLabel-defradio2">
                                            WebMoney
                                            <img class="payment-methods__img" src="/styles/order-new/img/payment/pay-webmoney.png">
                                        </label>
                                    </li>
                                    <li class="payment-methods__i">
                                        <input id="payment-qiwi" type="radio" name="payment-type[]" value="by_qiwi" class="customInput customInput-defradio2 jsPaymentMethodRadio js-customInput" >
                                        <label for="payment-qiwi" class="customLabel customLabel-defradio2">
                                            Qiwi
                                            <img class="payment-methods__img" src="/styles/order-new/img/payment/pay-qiwi.png">
                                        </label>
                                    </li>

                                    <li class="payment-methods__i top-space"><!-- ставим класс top-space на элемент, который имеет сверху бОльший оступ-->
                                        <input id="payment-psb" type="radio" name="payment-type[]" value="by_psb" class="customInput customInput-defradio2 jsPaymentMethodRadio js-customInput" >
                                        <label for="payment-psb" class="customLabel customLabel-defradio2">
                                            Выставить счет в PSB
                                            <img class="payment-methods__img" src="/styles/order-new/img/payment/pay-psb.png">
                                        </label>
                                    </li>

                                </ul>
                                <!-- END Новые способы оплаты - статика -->
                                <div class="payments-popup__pay">
                                    <button class="orderPayment_btn btn3">Оплатить онлайн</button>
                                    <p class="orderPayment_msg_hint">Вы будете перенаправлены на сайт платежной системы.</p>
                                </div>
                            </div>
                            <!-- END popup оплаты -->
                            </div>
                        <? endif ?>

                        <? if ($order->isPaid()) : ?>

                            <!-- Оплачено -->
                            <div class="orderLn_row orderLn_row-bg orderLn_row-bg-grey jsOrderPaid">
                                <img class="orderLn_row_imgpay" src="/styles/order/img/payment.png" alt="">
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

<? };



