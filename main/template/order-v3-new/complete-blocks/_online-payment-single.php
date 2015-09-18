<?php

$f = function(
    \Helper\TemplateHelper $helper
) {
?>

    <!-- блок когда была выбран конкретный способ оплаты -->
    <div class="orderPayment orderPaymentWeb jsOnlinePaymentPossible jsOnlinePaymentPossibleNoMotiv">
        <!-- Заголовок-->
        <!-- Блок в обводке -->
        <div class="orderPayment_block orderPayment_noOnline">

            <div class="orderPayment_msg orderPayment_noOnline_msg">
                <div class="orderPayment_msg_head">
                    Онлайн-оплата
                </div>

                <div class="orderPayment_msg_shop orderPayment_pay">
                    <!-- Здесь выводим иконки побольше -->
                    <div class="order-payment__choosed">
                        Qiwi <img src="/styles/order-new/img/payment/pay-qiwi-big.png">
                    </div>

                    <button class="orderPayment_btn btn3">Оплатить онлайн</button>
                </div>
                <p class="orderPayment_msg_hint">Вы будете перенаправлены на сайт платежной системы.</p>
            </div>
        </div>
    </div>
    <!-- END блок когда была выбран конкретный способ оплаты -->

<? }; return $f;