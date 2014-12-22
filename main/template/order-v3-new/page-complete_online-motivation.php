<?php

return function(
    \Helper\TemplateHelper $helper,
    $orders,
    $ordersPayment,
    $products,
    $userEntity,
    $sessionIsReaded,
    $banks,
    $creditData
) {
    /** @var $products \Model\Product\Entity[] */
    $page = new \View\OrderV3\CompletePage();
    /** @var $order \Model\Order\Entity */
    $order = reset($orders);
    ?>

    <?= $helper->render('order-v3-new/__head', ['step' => 3]) ?>

    <section class="orderCnt">

        <!-- Заверстать все блоки тут -->
        <!-- Блок оплата (оплата онлайн невозможна) -->
        <div class="orderPayment">
            <!-- Заголовок-->
            <div class="orderPayment_head">
                    Оформлен заказ № <a href="#" class="orderPayment_num">COXD-305127</a>
            </div>
            <!-- Блок в обводке -->
            <div class="orderPayment_block orderPayment_noOnline">

                <div class="orderPayment_msg orderPayment_noOnline_msg">
                    <div class="orderPayment_msg_head">
                        Ждем вас 27.12.2014 в магазине
                    </div>
                    <div class="orderPayment_msg_shop markerLst_row">
                        <span class="markerList_col markerList_col-mark">
                            <i class="markColor" style="background-color: #B61D8E"></i>
                        </span>
                        <span class="markerList_col">
                        <span class="orderPayment_msg_shop_metro">м. Петровско-Разумовская</span>
                        <span class="orderPayment_msg_shop_addr">ул. Линии Октябрьской Железной Дороги, д. 1, стр. 2</span>
                            <a href="#" class="orderPayment_msg_addr_link">
                                Как добраться
                            </a>
                        </span>
                    </div>
                    <div class="orderPayment_msg_info">
                        Вы можете оплатить заказ при получении.
                    </div>
                </div>
            </div>
        </div>

<!-- Блок заказ оплачен -->
        <div class="orderPayment orderPaid">
            <!-- Заголовок-->
            <!-- Блок в обводке -->
            <div class="orderPayment_block orderPayment_noOnline">

                <div class="orderPayment_msg orderPayment_noOnline_msg">
                    <div class="orderPayment_msg_head">
                        Ждем вас 27.12.2014 в магазине
                    </div>
                    <div class="orderPayment_msg_shop markerLst_row">
                        <span class="markerList_col markerList_col-mark">
                            <i class="markColor" style="background-color: #B61D8E"></i>
                        </span>
                        <span class="markerList_col">
                            <span class="orderPayment_msg_shop_metro">м. Петровско-Разумовская</span>
                            <span class="orderPayment_msg_shop_addr">ул. Линии Октябрьской Железной Дороги, д. 1, стр. 2</span>
                            <a href="#" class="orderPayment_msg_addr_link">
                                Как добраться
                            </a>
                        </span>
                    </div>
                    <div class="orderPayment_msg_info">
                        <span class="orderPayment_msg_info_status">Заказ оплачен</span>
                        <div class="orderPaymentWeb_lst_sys-logo noFlnoWdt"><img src="/styles/order/img/logo-yamoney.jpg"></div>
                    </div>
                </div>
            </div>
        </div>

<!-- Блок оплата в кредит -->
        <div class="orderPayment orderPaymentCr">
            <!-- Заголовок-->
            <!-- Блок в обводке -->
            <div class="orderPayment_block orderPayment_noOnline">

                <div class="orderPayment_msg orderPayment_noOnline_msg">
                    <div class="orderPayment_msg_head">
                        Заявка на кредит
                    </div>
                    <ul class="orderPaymentCr_lst clearfix">
                        <li class="orderPaymentCr_lst-i"><a href="#">
                                <img class="orderPaymentCr_lst_bank-logo" src="/styles/order/img/tks-logo.jpg">
                                <button class="orderPayment_btn orderPayment_btn-toggle btn3 btn3-shadow">Заполнить</button>
                                <span class="orderPaymentCr_lst_bank">Банк Тинькофф
                                    <span class="pb-small">Условия кредитования</span>
                                </span>
                            </a>
                        </li>
                        <li class="orderPaymentCr_lst-i"><a href="#">
                                <img class="orderPaymentCr_lst_bank-logo" src="/styles/order/img/renessance-logo.jpg">
                                <button class="orderPayment_btn orderPayment_btn-toggle btn3 btn3-shadow">Заполнить</button>
                                <span class="orderPaymentCr_lst_bank">Ренессанс кредит
                                    <span class="pb-small">Условия кредитования</span>
                                </span>
                            </a>
                        </li>
                        <li class="orderPaymentCr_lst-i"><a href="#">
                                <img class="orderPaymentCr_lst_bank-logo" src="/styles/order/img/otp-logo.jpg">
                                <button class="orderPayment_btn orderPayment_btn-toggle btn3 btn3-shadow">Заполнить</button>
                                <span class="orderPaymentCr_lst_bank" >ОТР Банк
                                <span class="pb-small">Условия кредитования</span></span>
                            </a>
                        </li>
                    </ul>
                    <div class="orderPayment_msg_info">
                        <a href="" class="orderPaymentCr_other_link">
                            Не оформлять кредит
                            <span class="pb-small">оплатить онлайн или при получении</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

<!-- Блок оплата в кредит -->
        <div class="orderPayment orderPaymentCr">
            <!-- Заголовок-->
            <!-- Блок в обводке -->
            <div class="orderPayment_block orderPayment_noOnline">

                <div class="orderPayment_msg orderPayment_noOnline_msg">
                    <div class="orderPayment_msg_head">
                        Заявка на кредит
                    </div>
                    <ul class="orderPaymentCr_lst clearfix">
                        <li class="orderPaymentCr_lst-i"><a href="#">
                                <img class="orderPaymentCr_lst_bank-logo" src="/styles/order/img/tks-logo.jpg">
                                <button class="orderPayment_btn orderPayment_btn-toggle btn3 btn3-shadow">Заполнить</button>
                                <span class="orderPaymentCr_lst_bank">Банк Тинькофф
                                    <span class="pb-small">Условия кредитования</span>
                                </span>
                            </a>
                        </li>
                        <li class="orderPaymentCr_lst-i"><a href="#">
                                <img class="orderPaymentCr_lst_bank-logo" src="/styles/order/img/renessance-logo.jpg">
                                <button class="orderPayment_btn orderPayment_btn-toggle btn3 btn3-shadow">Заполнить</button>
                                <span class="orderPaymentCr_lst_bank">Ренессанс кредит
                                    <span class="pb-small">Условия кредитования</span>
                                </span>
                            </a>
                        </li>
                        <li class="orderPaymentCr_lst-i"><a href="#">
                                <img class="orderPaymentCr_lst_bank-logo" src="/styles/order/img/otp-logo.jpg">
                                <button class="orderPayment_btn orderPayment_btn-toggle btn3 btn3-shadow">Заполнить</button>
                                <span class="orderPaymentCr_lst_bank" >ОТР Банк
                                <span class="pb-small">Условия кредитования</span></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
<!-- Блок оплата платежные системы -->
        <div class="orderPayment orderPaymentWeb">
            <!-- Заголовок-->
            <!-- Блок в обводке -->
            <div class="orderPayment_block orderPayment_noOnline">

                <div class="orderPayment_msg orderPayment_noOnline_msg">
                    <div class="orderPayment_msg_head">
                        К оплате: 47 638 <span class="rubl">p</span>
                    </div>
                      <ul class="orderPaymentWeb_lst clearfix">
                        <li class="orderPaymentWeb_lst-i">
                            <div class="orderPaymentWeb_lst_sys-logo"><img class="orderPaymentWeb_lst_sys-logo-img" src="/styles/order/img/logo-visa.jpg"></div>
                            <button class="orderPayment_btn orderPayment_btn-toggle btn3">Оплатить</button>
                            <a class="orderPaymentWeb_lst_sys" href="#">Банковская карта
                            </a>

                        </li>
                        <li class="orderPaymentWeb_lst-i">
                            <div class="orderPaymentWeb_lst_sys-logo"><img class="orderPaymentWeb_lst_sys-logo-img" src="/styles/order/img/logo-paypal.jpg"></div>
                            <button class="orderPayment_btn orderPayment_btn-toggle btn3">Оплатить</button>
                            <a class="orderPaymentWeb_lst_sys" href="#">PayPal
                            </a>

                        </li>
                        <li class="orderPaymentWeb_lst-i">
                            <div class="orderPaymentWeb_lst_sys-logo"><img class="orderPaymentWeb_lst_sys-logo-img" src="/styles/order/img/logo-yamoney.jpg"></div>
                            <button class="orderPayment_btn orderPayment_btn-toggle btn3">Оплатить</button>
                            <a class="orderPaymentWeb_lst_sys" href="#">Яндекс.Деньги</a>

                        </li>
                        <li class="orderPaymentWeb_lst-i">
                            <div class="orderPaymentWeb_lst_sys-logo"> <img src="/styles/order/img/logo-psb.jpg"></div>
                            <button class="orderPayment_btn orderPayment_btn-toggle btn3">Оплатить</button>
                            <a class="orderPaymentWeb_lst_sys" href="#">Интернет-банк Промсвязьбанка</a>

                        </li>
                    </ul>
                    <div class="orderPayment_msg_info">
                        Вы будете перенаправлены на сайт платежной системы
                    </div>
                </div>
            </div>
        </div>

        <!-- Блок доставка -->
        <div class="orderPayment orderDelivery">
            <!-- Заголовок-->
            <!-- Блок в обводке -->
            <div class="orderPayment_block orderPayment_noOnline">

                <div class="orderPayment_msg orderPayment_noOnline_msg">
                    <div class="orderPayment_msg_head">
                        Доставка назачена на 27.12.2014
                    </div>
                    <div class="orderPayment_msg_shop markerLst_row">
                        <span class="markerList_col markerList_col-mark">
                            <i class="markColor" style="background-color: #B61D8E"></i>
                        </span>
                        <span class="markerList_col">
                            <span class="orderPayment_msg_shop_metro">м. Пролетарская</span>
                            <span class="orderPayment_msg_shop_addr">ул. Абельмановская, д. 1, стр. 2</span>
                            <div class="orderPayment_msg_adding">Дополнительные пожелания "не рвите коробку"</div>
                        </span>

                    </div>
                    <div class="orderPayment_msg_info">
                        Оплата заказа наличными при получении.
                    </div>
                </div>
            </div>
        </div>

<!-- Блок оплата в два клика-->
        <div class="orderPayment orderPaymentWeb">
            <!-- Заголовок-->
            <!-- Блок в обводке -->
            <div class="orderPayment_block orderPayment_noOnline">

                <div class="orderPayment_msg orderPayment_noOnline_msg">
                    <div class="orderPayment_msg_head">
                        Онлайн-оплата в два клика
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

<!-- Блок оплата платежные системы -->
        <div class="orderPayment orderPaymentWeb">
            <!-- Заголовок-->
            <!-- Блок в обводке -->
            <div class="orderPayment_block orderPayment_noOnline">

                <div class="orderPayment_msg orderPayment_noOnline_msg">
                    <div class="orderPayment_msg_head">
                        Онлайн-оплата в два клика
                    </div>
                    <ul class="orderPaymentWeb_lst clearfix">
                        <li class="orderPaymentWeb_lst-i">
                            <div class="orderPaymentWeb_lst_sys-logo"><img class="orderPaymentWeb_lst_sys-logo-img" src="/styles/order/img/logo-visa.jpg"></div>
                            <button class="orderPayment_btn orderPayment_btn-toggle btn3">Оплатить</button>
                            <a class="orderPaymentWeb_lst_sys" href="#">Банковская карта
                            </a>
                        </li>
                        <li class="orderPaymentWeb_lst-i">
                            <div class="orderPaymentWeb_lst_sys-logo"><img class="orderPaymentWeb_lst_sys-logo-img" src="/styles/order/img/logo-paypal.jpg"></div>
                            <button class="orderPayment_btn orderPayment_btn-toggle btn3">Оплатить</button>
                            <a class="orderPaymentWeb_lst_sys" href="#">PayPal
                            </a>
                        </li>
                        <li class="orderPaymentWeb_lst-i">
                            <div class="orderPaymentWeb_lst_sys-logo"><img class="orderPaymentWeb_lst_sys-logo-img" src="/styles/order/img/logo-yamoney.jpg"></div>
                            <button class="orderPayment_btn orderPayment_btn-toggle btn3">Оплатить</button>
                            <a class="orderPaymentWeb_lst_sys" href="#">Яндекс.Деньги</a>
                        </li>
                        <li class="orderPaymentWeb_lst-i">
                            <div class="orderPaymentWeb_lst_sys-logo"> <img src="/styles/order/img/logo-psb.jpg"></div>
                            <button class="orderPayment_btn orderPayment_btn-toggle btn3">Оплатить</button>
                            <a class="orderPaymentWeb_lst_sys" href="#">Интернет-банк Промсвязьбанка</a>
                        </li>
                    </ul>
                    <div class="orderPayment_msg_info">
                        Вы будете перенаправлены на сайт платежной системы
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
                        <li class="orderPaymentWeb_lst-i">
                            <div class="orderPaymentWeb_lst_sys-logo"><img class="orderPaymentWeb_lst_sys-logo-img" src="/styles/order/img/logo-paypal.jpg"></div>
                            <button class="orderPayment_btn orderPayment_btn-toggle btn3">Оплатить</button>
                            <a class="orderPaymentWeb_lst_sys" href="#">PayPal
                            </a>
                        </li>
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
        <div class="orderCompl orderCompl_final clearfix">
            <a class="orderCompl_continue_link" href="<?= $helper->url('homepage') ?>">Вернуться на главную</a>
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



