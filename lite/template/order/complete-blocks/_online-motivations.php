<? $f  = function () { ?>

<!-- Блок оплата в два клика(VIP)-->
<div class="orderPayment orderPaymentWeb">
    <!-- Заголовок-->
    <!-- Блок в обводке -->
    <div class="orderPayment_block orderPayment_noOnline">

        <div class="orderPayment_msg orderPayment_noOnline_msg">
            <div class="orderPayment_msg_head">
                Заберите заказ без очереди<br/>при оплате онлайн
            </div>
            <div class="orderPayment_msg_shop orderPayment_pay">
                <button class="orderPayment_btn btn3">Оплатить</button>
                <ul class="orderPaymentWeb_lst-sm">
                    <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src ="/styles/order/img/visa-logo-sm.jpg"></a></li>
                    <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src ="/styles/order/img/yamoney-sm.jpg"></a></li>
                    <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src ="/styles/order/img/paypal.png"></a></li>
                    <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src ="/styles/order/img/psb.png"></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Блок оплата в два клика ( купон )-->
<div class="orderPayment orderPaymentWeb">
    <!-- Заголовок-->
    <!-- Блок в обводке -->
    <div class="orderPayment_block orderPayment_noOnline">

        <div class="orderPayment_msg orderPayment_noOnline_msg">
            <div class="orderPayment_msg_head">
                Скидка 6% на следующий заказ<br/>при оплате онлайн
            </div>
            <div class="orderPayment_msg_shop orderPayment_pay">
                <button class="orderPayment_btn btn3">Оплатить</button>
                <ul class="orderPaymentWeb_lst-sm">
                    <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src ="/styles/order/img/visa-logo-sm.jpg"></a></li>
                    <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src ="/styles/order/img/yamoney-sm.jpg"></a></li>
                    <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src ="/styles/order/img/paypal.png"></a></li>
                    <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src ="/styles/order/img/psb.png"></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Блок оплата в два клика (дисконт 1)-->
<div class="orderPayment orderPaymentWeb">
    <!-- Заголовок-->
    <!-- Блок в обводке -->
    <div class="orderPayment_block orderPayment_noOnline">

        <div class="orderPayment_msg orderPayment_noOnline_msg">
            <div class="orderPayment_msg_head">
                <div class="orderPayment_msg_head-row">
                    <label class="orderSum-lbl">Сумма заказа:</label>
                    <span class="orderSum">47 690 <span class="rubl">p</span></span>
                </div>
                <div class="orderPayment_msg_head-row">
                    <label class="orderSum-lbl">При оплате онлайн:</label>
                    <span class="orderSum">46 259 <span class="rubl">p</span><span class="pr-dsc">–3%</span></span>
                </div>

            </div>
            <div class="orderPayment_msg_shop orderPayment_pay">
                <button class="orderPayment_btn btn3">Оплатить</button>
                <ul class="orderPaymentWeb_lst-sm">
                    <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src ="/styles/order/img/visa-logo-sm.jpg"></a></li>
                    <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src ="/styles/order/img/yamoney-sm.jpg"></a></li>
                    <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src ="/styles/order/img/paypal.png"></a></li>
                    <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src ="/styles/order/img/psb.png"></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Блок оплата платежные системы скидка 6%-->
<div class="orderPayment orderPaymentWeb">
    <!-- Заголовок-->
    <!-- Блок в обводке -->
    <div class="orderPayment_block orderPayment_noOnline">

        <div class="orderPayment_msg orderPayment_noOnline_msg">
            <div class="orderPayment_msg_head">
                Скидка 6% на следующий заказ<br/>при оплате онлайн
            </div>
            <ul class="orderPaymentWeb_lst clearfix">
                <li class="orderPaymentWeb_lst-i">
                    <div class="orderPaymentWeb_lst_sys-logo"><img class="orderPaymentWeb_lst_sys-logo-img" src="/styles/order/img/logo-visa.jpg"></div>
                    <button class="orderPayment_btn orderPayment_btn-toggle btn3">Оплатить</button>
                    <a class="orderPaymentWeb_lst_sys" href="#">Банковская карта
                    </a>
                </li>
                <? if (false): ?>
                <li class="orderPaymentWeb_lst-i">
                    <div class="orderPaymentWeb_lst_sys-logo"><img class="orderPaymentWeb_lst_sys-logo-img" src="/styles/order/img/logo-paypal.jpg"></div>
                    <button class="orderPayment_btn orderPayment_btn-toggle btn3">Оплатить</button>
                    <a class="orderPaymentWeb_lst_sys" href="#">PayPal
                    </a>
                </li>
                <? endif ?>
                <li class="orderPaymentWeb_lst-i">
                    <div class="orderPaymentWeb_lst_sys-logo"><img class="orderPaymentWeb_lst_sys-logo-img" src="/styles/order/img/logo-yamoney.jpg"></div>
                    <button class="orderPayment_btn orderPayment_btn-toggle btn3">Оплатить</button>
                    <a class="orderPaymentWeb_lst_sys" href="#">Яндекс.Деньги</a>
                </li>
                <li class="orderPaymentWeb_lst-i">
                    <div class="orderPaymentWeb_lst_sys-logo"><img class="orderPaymentWeb_lst_sys-logo-img" src="/styles/order/img/logo-psb.jpg"></div>
                    <button class="orderPayment_btn orderPayment_btn-toggle btn3">Оплатить</button>
                    <a class="orderPaymentWeb_lst_sys" href="#">Интернет-банк Промсвязьбанка</a>
                </li>
            </ul>
            <div class="orderPayment_msg_info">
                Вы будете перенаправлены на сайт платежной системы.<br/>
                Код скидки вы получите на email и по СМС.
            </div>
        </div>
    </div>
</div>

<? }; return $f; ?>