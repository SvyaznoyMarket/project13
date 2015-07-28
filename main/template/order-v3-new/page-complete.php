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
<?= $helper->render('order-v3-new/__head', ['step' => 3]) ?>

    <section class="orderCnt jsOrderV3PageComplete">
        <h1 class="orderCnt_t">Ваши заказы</h1>

        <div class="orderLnSet">

            <? foreach ($orders as $order): ?>
            <? /** @var $order \Model\Order\Entity */?>

                <div class="orderLn clearfix" data-order-id="<?= $order->getId() ?>" data-order-number="<?= $order->getNumber() ?>" data-order-number-erp="<?= $order->getNumberErp() ?>">
                    <div class="orderLn_l">

                        <? if ($userEntity) : ?>
                            <div class="orderLn_row orderLn_row-t"><strong>Заказ</strong> <a href="<?= \App::router()->generate('user.order', ['orderId' =>$order->getId()]) ?>"><?= $order->getNumberErp()?></a>
                                <? if ($order->getMetaByKey('special_action')) : ?><div>Требуется предоплата</div><? endif ?>
                            </div>
                        <? else : ?>
                            <div class="orderLn_row orderLn_row-t"><strong>Заказ</strong> <?= $order->getNumberErp()?>
                                <? if ($order->getMetaByKey('special_action')) : ?><div class="order-alert order-alert--big">Требуется предоплата</div><? endif ?>
                            </div>
                        <? endif ?>

                        <? if ($order->getMetaByKey('special_action')) : ?><div>Требуется предоплата</div><? endif ?>

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
                        <div><?= \RepositoryManager::deliveryType()->getEntityById($order->deliveryTypeId)->getShortName() ?>
                            <? if ($order->deliveredAt) : ?><?= strftime('%e %b %Y', $order->deliveredAt->getTimestamp()) ?><? endif ?>
                            <? if ($order->interval) : ?><?= $order->interval->getStart()?>…<?= $order->interval->getEnd() ?><? endif ?>
                        </div>
                        <!--<div>Оплата при получении: наличные, банковская карта</div>-->
                    </div>

                    <? endif ?>

                    <div class="orderLn_r">
                        <? if ($order->getPaySum()): ?>
                            <div class="orderLn_row orderLn_row-summ">
                                <span class="summT">Сумма заказа:</span>
                                <span class="summP"><?= $helper->formatPrice($order->getPaySum()) ?> <span class="rubl">p</span></span>
                            </div>
                        <? endif ?>

                        <? if ($order->isPaid()) : ?>

                            <!-- Оплачено -->
                            <div class="orderLn_row orderLn_row-bg orderLn_row-bg-grey jsOrderPaid">
                                <img class="orderLn_row_imgpay" src="/styles/order/img/payment.png" alt="">
                            </div>

                        <? else : ?>

                        <? if (isset($ordersPayment[$order->getNumber()])) : ?>
                        <? $paymentEntity = $ordersPayment[$order->getNumber()]; /** @var $paymentEntity \Model\PaymentMethod\PaymentEntity */?>

                            <? if (isset($paymentEntity->groups[2])) : ?>

                            <div class="orderLn_row orderLn_row-bg jsOnlinePaymentBlock">

                                <? if (isset($paymentEntity->methods[\Model\PaymentMethod\PaymentMethod\PaymentMethodEntity::PAYMENT_CREDIT])
                                        && $order->isCredit() ) : ?>

                                    <!-- Кредит -->

                                    <div class="payT">Покупка в кредит</div>
                                    <a href="" class="btnLightGrey jsCreditButton"><strong>Заполнить заявку</strong></a>

                                    <ul style="display: none;" class="customSel_lst popupFl customSel_lst-pay jsCreditList">
                                        <? foreach ($banks as $bank) : ?>
                                            <? /** @var $bank \Model\CreditBank\Entity */?>
                                            <li class="customSel_i jsPaymentMethod" data-value="<?= $bank->getId() ?>" data-bank-provider-id="<?= $bank->getProviderId() ?>">
                                                <img src="<?= $bank->getImage() ?>" />
<!--                                                <strong>--><?//= $bank->getName() ?><!--</strong><br/>-->
<!--                                                --><?//= $bank->getDescription() ?><!--<br/>-->
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

                                        <div class="payT">Требуется <span class="payBtn btn4 jsOnlinePaymentSpan"><span class="brb-dt">предоплата</span></span></div>
                                        <div class="orderLn_box jsOnlinePaymentBlock">
                                            <a href="" class="orderLn_btn btnLightGrey">
                                                <? foreach ($paymentMethods as $method) : ?>
                                                    <img src="<?= $method->icon ?>" alt="" />
                                                <? endforeach ?>
                                            </a>
                                        </div>
                                        <ul style="display: none;" class="customSel_lst popupFl jsOnlinePaymentList">
                                            <? foreach ($paymentMethods as $method) : ?>
                                                <li class="customSel_i jsPaymentMethod" data-value="<?= $method->id ?>">
                                                    <strong><?= $method->name ?></strong><br/>
                                                    <?= $method->description ?>
                                                </li>
                                            <? endforeach ?>
                                        </ul>

                                    <? else : ?>

                                        <div class="payT">Можно <span class="payBtn btn4 jsOnlinePaymentSpan"><span class="brb-dt">оплатить онлайн</span></span></div>

                                        <? foreach ($paymentMethods as $method) : ?>
                                            <img src="<?= $method->icon ?>" alt="" />
                                        <? endforeach ?>

                                        <ul style="display: none;" class="customSel_lst popupFl jsOnlinePaymentList">
                                        <? foreach ($paymentMethods as $method) : ?>
                                            <li class="customSel_i jsPaymentMethod" data-value="<?= $method->id ?>">
                                                <strong><?= $method->name ?></strong><br/>
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



