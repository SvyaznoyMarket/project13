<? $f  = function (
    \Model\Order\Entity $order,
    \Model\PaymentMethod\PaymentEntity $orderPayment,
    $action
) {

    $helper = \App::helper();
    // по идее в $orderPayment->methods уже только методы, которые содержат акцию, поэтому берем первый метод и смотрим на сумму
    /** @var $method \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity */
    $sumWithDiscount = $order->getSum();
    $method = reset($orderPayment->methods);
    $actionArr = !is_null($method) ? $method->getAction($action) : null;
    if (is_array($actionArr)) $sumWithDiscount = $actionArr['payment_sum'];

    ?>

<? if ($action == 'online_motivation_coupon') : ?>
        <!-- Блок в обводке -->
        <div class="orderPayment_block orderPayment_noOnline orderPayment_block--border">

            <div class="orderPayment_msg orderPayment_noOnline_msg">
                <div class="orderPayment_msg_head">
                    Скидка 10% на следующий заказ<br/>при оплате онлайн
                </div>
                <!-- Этот блок показывается сразу -->
                <div class="jsOrderCouponInitial">
                    <div class="orderPayment_msg_shop orderPayment_pay">
                        <button class="orderPayment_btn btn3 ">Оплатить сейчас</button>
                        <ul class="orderPaymentWeb_lst-sm">
                            <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src ="/styles/order/img/visa-logo-sm.jpg"></a></li>
                            <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src ="/styles/order/img/psb.png"></a></li>
                        </ul>
                        <a class="orderPayment_msg_link orderPayment_msg_link--mid" href="/how_use_chip">Подробнее</a>
                    </div>
                </div>
                <!-- А этот потом -->
                <div class="jsOrderCouponExpanded" style="display: none">
                    <ul class="orderPaymentWeb_lst clearfix">
                        <?= $helper->render('order-v3-new/complete-blocks/__payments-li', ['orderPayment' => $orderPayment]) ?>
                    </ul>
                    <div class="orderPayment_msg_info">
                        Вы будете перемещены на сайт платежной системы.<br/><br/>
                        Код скидки будет распечатан на чеке.<br/>
                        <a class="orderPayment_msg_link" href="/how_use_chip" target="_blank">Как применить скидку</a>
                    </div>
                </div>
            </div>
        </div>
<? endif ?>

<? if ($action == 'online_motivation_discount') : ?>

        <div class="orderPayment_block orderPayment_noOnline jsOnlinePaymentPossible orderPayment_block--border">

            <div class="orderPayment_msg orderPayment_noOnline_msg">
                <div class="orderPayment_msg_head">
                    Скидка 5%
                </div>

                <div class="orderPayment_msg_head-row">
                    <label class="orderSum-lbl">Сумма заказа:</label>
                    <span class="orderSum"><?= $helper->formatPrice($order->sum) ?> <span class="rubl">p</span></span>
                </div>
                <div class="orderPayment_msg_head-row">
                    <label class="orderSum-lbl">При оплате онлайн:</label>
                    <span class="orderSum"><?= $helper->formatPrice($sumWithDiscount) ?> <span class="rubl">p</span></span>
                </div>


                <div class="orderPayment_msg_shop orderPayment_pay">
                    <button class="orderPayment_btn btn3 ">Оплатить сейчас</button>
                    <ul class="orderPaymentWeb_lst-sm">
                        <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src ="/styles/order/img/visa-logo-sm.jpg"></a></li>
                        <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src ="/styles/order/img/psb.png"></a></li>
                    </ul>
                </div>
            </div>
        </div>
<? endif ?>

<? }; return $f;