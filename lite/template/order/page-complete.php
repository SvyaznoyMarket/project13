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
<section class="checkout jsOrderV3PageComplete">
        <h1 class="checkout__title">Ваши заказы</h1>

        <div class="checkout-complete">

            <? foreach ($orders as $order): ?>
                <? /** @var $order \Model\Order\Entity */?>

                <div class="checkout-complete-box" data-order-id="<?= $order->getId() ?>" data-order-number="<?= $order->getNumber() ?>" data-order-number-erp="<?= $order->getNumberErp() ?>">
                    <div class="checkout-complete-box__head">
                        <? if ($userEntity) : ?>
                            <div class="checkout-complete-box__title">Заказ <a href="<?= \App::router()->generate('user.order', ['orderId' =>$order->getId()]) ?>"><?= $order->getNumberErp()?></a></div>
                        <? else : ?>
                            <div class="checkout-complete-box__title">Заказ <?= $order->getNumberErp()?></div>
                        <? endif ?>

                        <? if ($order->getPaySum()): ?>
                            <div class="checkout-complete-box__summ">
                                <span class="checkout-complete-box__summ-title">Сумма заказа:</span>
                                <span class="checkout-complete-box__summ-value"><?= $helper->formatPrice($order->getPaySum()) ?>&thinsp;<span class="rubl">C</span></span>
                            </div>
                        <? endif ?>
                    </div>

                    <div class="checkout-complete-box__content table">
                        <div class="checkout-complete-box__left table-cell">
                            <ul class="checkout-complete-orders">
                                <? foreach ($order->getProduct() as $key => $product): ?>
                                    <? /** @var $product \Model\Order\Product\Entity */?>
                                    <? if (isset($products[$product->getId()])) : ?>
                                        <li class="checkout-complete-orders__item"><?= $products[$product->getId()]->getPrefix() == '' ? mb_strimwidth($products[$product->getId()]->getName(), 0, 40, '…') :  mb_strimwidth($products[$product->getId()]->getPrefix(), 0, 40, '…') ?> <?= $product->getQuantity() ?> шт.</li>
                                    <? endif ?>
                                    <? if ($key == 2 && count($order->getProduct()) > 3) : ?>
                                        <? $orderProductsString = $helper->numberChoiceWithCount(count($order->getProduct()) - 3, ['товар', 'товара', 'товаров']) ?>
                                        <? if ($userEntity) : ?>
                                            <li class="checkout-complete-orders__item"><a class="" href="<?= \App::router()->generate('user.order', ['orderId' =>$order->getId()]) ?>">и ещё <?= $orderProductsString ?></a></li>
                                        <? else : ?>
                                            <li class="checkout-complete-orders__item">и ещё <?= $orderProductsString ?></li>
                                        <? endif ?>
                                        <? break; endif ?>
                                <? endforeach ?>
                            </ul>

                        </div>

                        <? if (\RepositoryManager::deliveryType()->getEntityById($order->deliveryTypeId)) : ?>

                            <div class="checkout-complete-box__center table-cell">
                                <div><?= \RepositoryManager::deliveryType()->getEntityById($order->deliveryTypeId)->getShortName() ?>
                                    <? if ($order->deliveredAt) : ?><?= strftime('%e %b %Y', $order->deliveredAt->getTimestamp()) ?><? endif ?>
                                    <? if ($order->interval) : ?><?= $order->interval->getStart()?>…<?= $order->interval->getEnd() ?><? endif ?>
                                </div>
                                <!--<div>Оплата при получении: наличные, банковская карта</div>-->
                            </div>

                        <? endif ?>

                        <div class="checkout-complete-box__right table-cell">
                            <? if ($order->isPaid()) : ?>

                                <!-- Оплачено -->
                                <div class="checkout-complete-box__row jsOrderPaid">
                                    <img class="" src="/styles/order/img/payment.png" alt="">
                                </div>

                            <? else : ?>

                                <? if (isset($ordersPayment[$order->getNumber()])) : ?>
                                    <? $paymentEntity = $ordersPayment[$order->getNumber()]; /** @var $paymentEntity \Model\PaymentMethod\PaymentEntity */?>

                                    <? if (isset($paymentEntity->groups[2])) : ?>

                                        <div class="checkout-complete-payment jsOnboxPaymentBlock">

                                            <? if (isset($paymentEntity->methods[\Model\PaymentMethod\PaymentMethod\PaymentMethodEntity::PAYMENT_CREDIT])
                                                && $order->isCredit() ) : ?>

                                                <!-- Кредит -->

                                                <div class="checkout-complete-payment__title">Покупка в кредит</div>
                                                <a href="" class="jsCreditButton">Заполнить заявку</a>

                                                <ul style="display: none;" class="customSel_lst popupFl customSel_lst-pay jsCreditList">
                                                    <? foreach ($banks as $bank) : ?>
                                                        <li class="customSel_i jsPaymentMethod" data-value="<?= $bank->getId() ?>" data-bank-provider-id="<?= $bank->getProviderId() ?>">
                                                            <img src="<?= $bank->getImage() ?>" />
                                                            <a href="<?= $bank->getLink() ?>" target="_blank" style="float: right">Условия кредитования</a>
                                                        </li>
                                                    <? endforeach ?>
                                                </ul>

                                                <? if (isset($creditData[$order->getNumber()])) : ?>
                                                    <div class="credit-widget" data-value="<?= $helper->json($creditData[$order->getNumber()]) ?>"></div>
                                                <? endif ?>

                                            <? elseif (isset($paymentEntity->groups[\Model\PaymentMethod\PaymentGroup\PaymentGroupEntity::PAYMENT_NOW])) : ?>
                                                <? $paymentMethods = array_filter($paymentEntity->methods, function (\Model\PaymentMethod\PaymentMethod\PaymentMethodEntity $method) use ($paymentEntity) {return $method->paymentGroup === $paymentEntity->groups[\Model\PaymentMethod\PaymentGroup\PaymentGroupEntity::PAYMENT_NOW]; }) ?>

                                                <!-- Онлайн-оплата -->

                                                <? if ($order->sum > \App::config()->order['prepayment']['priceLimit']) : ?>

                                                    <div class="checkout-complete-payment__title">Требуется <span class="payBtn btn4 jsOnboxPaymentSpan"><span class="brb-dt">предоплата</span></span></div>
                                                    <div class="orderLn_box jsOnboxPaymentBlock">
                                                        <a href="" class="orderLn_btn btnLightGrey">
                                                            <? foreach ($paymentMethods as $method) : ?>
                                                                <img src="<?= $method->icon ?>" alt="" />
                                                            <? endforeach ?>
                                                        </a>
                                                    </div>
                                                    <ul style="display: none;" class="customSel_lst popupFl jsOnlinePaymentList">
                                                        <? foreach ($paymentMethods as $method) : ?>
                                                            <li class="customSel_i jsPaymentMethod" data-value="<?= $method->id ?>">
                                                                <?= $method->name ?><br/>
                                                                <?= $method->description ?>
                                                            </li>
                                                        <? endforeach ?>
                                                    </ul>

                                                <? else : ?>

                                                    <div class="checkout-complete-payment__title">Оплатить онлайн</div>

                                                    <button class="checkout-complete-payment__btn btn-primary jsOnlinePaymentSpan">Оплатить</button>
                                                    <? foreach ($paymentMethods as $method) : ?>
                                                        <img class="checkout-complete-payment__img" src="<?= $method->icon ?>" alt="" />
                                                    <? endforeach ?>

                                                    <ul style="display: none;" class="customSel_lst popupFl jsOnlinePaymentList">
                                                        <? foreach ($paymentMethods as $method) : ?>
                                                            <li class="customSel_i jsPaymentMethod" data-value="<?= $method->id ?>">
                                                                <?= $method->name ?><br/>
                                                                <?= $method->description ?>
                                                            </li>
                                                        <? endforeach ?>
                                                    </ul>

                                                <? endif ?>

                                            <? endif ?>

                                        </div>

                                    <? endif ?>

                                <? endif ?>

                            <? endif ?>
                        </div>
                    </div>
                </div>

            <? endforeach ?>

        </div>

        <div class="checkout-continue">
            <a class="checkout-continue__link underline" href="<?= $helper->url('homepage') ?>">Вернуться на главную</a>
        </div>
    </section>

    <? if (false && !$sessionIsReaded): ?>
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