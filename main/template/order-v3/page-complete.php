<?php

return function(
    \Helper\TemplateHelper $helper,
    $orders,
    $ordersPayment,
    $products
) {

?>

<?= $helper->render('order-v3/__head', ['step' => 3]) ?>

    <section class="orderCnt">
        <h1 class="orderCnt_t">Ваши заказы</h1>

        <div class="orderLnSet">

            <? foreach ($orders as $order): ?>
            <? /** @var $order \Model\Order\Entity */?>

                <div class="orderLn clearfix">
                    <div class="orderLn_l">
                        <div class="orderLn_row orderLn_row-t"><strong>Заказ</strong> <a href=""><?= $order->getNumberErp()?></a></div>

                        <? if (isset($products[$order->getNumber()]) && (bool)$products[$order->getNumber()] ) : ?>

                        <ul class="orderLn_lst">
                            <? foreach ($products[$order->getNumber()] as $key => $product): ?>
                            <? /** @var $product \Model\Product\Entity */?>
                            <li class="orderLn_lst_i"><?= $product->getWebName()?> 3 шт.</li>
                            <li class="orderLn_lst_i"><a class="orderLn_lst_lk" href="">и ещё 3 товара</a></li>
                            <? endforeach ?>
                        </ul>

                        <? endif ?>
                    </div>

                    <div class="orderLn_c"></div>

                    <div class="orderLn_r">
                        <div class="orderLn_row orderLn_row-summ">
                            <span class="summT">Сумма заказа:</span>
                            <span class="summP">42 740 <span class="rubl">p</span></span>
                        </div>

                        <div class="orderLn_row orderLn_row-bg">
                            <div class="payT">Покупка в кредит</div>

                            <a href="" class="btnLightGrey"><strong>Заполнить заявку</strong></a>
                        </div>
                    </div>
                </div>

            <? endforeach; ?>

            <div class="orderLn clearfix">
                <div class="orderLn_l">
                    <div class="orderLn_row orderLn_row-t"><strong>Заказ</strong> <a href="">COXD-305176</a></div>

                    <ul class="orderLn_lst">
                        <li class="orderLn_lst_i">Бумажный конструктор 3 шт.</li>
                        <li class="orderLn_lst_i">Батарейки AAAA 18 шт.</li>
                        <li class="orderLn_lst_i"><a class="orderLn_lst_lk" href="">и ещё 3 товара</a></li>
                    </ul>
                </div>

                <div class="orderLn_c"></div>

                <div class="orderLn_r">
                    <div class="orderLn_row orderLn_row-summ">
                        <span class="summT">Сумма заказа:</span>
                        <span class="summP">42 740 <span class="rubl">p</span></span>
                    </div>

                    <div class="orderLn_row orderLn_row-bg">
                        <div class="payT">Покупка в кредит</div>

                        <a href="" class="btnLightGrey"><strong>Заполнить заявку</strong></a>
                    </div>
                </div>
            </div>

            <div class="orderLn clearfix">
                <div class="orderLn_l">
                    <div class="orderLn_row orderLn_row-t"><strong>Заказ</strong> <a href="">COXD-305176</a></div>

                    <ul class="orderLn_lst">
                        <li class="orderLn_lst_i">Бумажный конструктор 3 шт.</li>
                        <li class="orderLn_lst_i">Батарейки AAAA 18 шт.</li>
                        <li class="orderLn_lst_i"><a class="orderLn_lst_lk" href="">и ещё 3 товара</a></li>
                    </ul>
                </div>

                <div class="orderLn_c">
                    <div>Самовывоз 11 июл. 2014 9:00…18:00</div>
                    <div>Оплата при получении: наличные, банковская карта</div>
                </div>

                <div class="orderLn_r">
                    <div class="orderLn_row orderLn_row-summ">
                        <span class="summT">Сумма заказа:</span>
                        <span class="summP">42 740 <span class="rubl">p</span></span>
                    </div>

                    <div class="orderLn_row orderLn_row-bg">
                        <div class="payT">Требуется предоплата</div>

                        <div class="orderLn_box">
                            <a href="" class="orderLn_btn btnLightGrey">
                                <strong class="btn4">Оплатить онлайн</strong><br/>
                                <img width="25" src="/styles/order/img/master.png" alt="" />
                                <img width="25" src="/styles/order/img/Visa.png" alt="" />
                                <img width="43" src="/styles/order/img/paypal.png" alt="" />
                                <img width="16" src="/styles/order/img/psb.png" alt="" />
                            </a>
                            <ul style="display: none;" class="customSel_lst popupFl">
                                <li class="customSel_i">
                                    <strong>PayPal</strong><br/>
                                    Вы можете оплатить ваш заказ прямо сейчас с помощью PayPal.
                                </li>

                                <li class="customSel_i">
                                    <strong>Онлайн оплата</strong><br/>
                                    Вы можете оплатить ваш заказ прямо сейчас. К оплате принимаются банковские карты платежных систем Visa, MasterCard, Diners Club, JCB. Услуга бесплатная, никаких дополнительных процентов вы не платите.
                                </li>

                                <li class="customSel_i">
                                    <strong>Выставить электронный счёт в личный кабинет Промсвязьбанка</strong><br/>
                                    Вы можете оплатить Ваш заказ путём выставления счёта в Ваш личный кабинет. Данная услуга доступна только для клиентов Промсвязьбанка.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="orderLn clearfix">
                <div class="orderLn_l">
                    <div class="orderLn_row orderLn_row-t"><strong>Заказ</strong> <a href="">COXD-305176</a></div>

                    <ul class="orderLn_lst">
                        <li class="orderLn_lst_i">Бумажный конструктор 3 шт.</li>
                        <li class="orderLn_lst_i">Батарейки AAAA 18 шт.</li>
                        <li class="orderLn_lst_i"><a class="orderLn_lst_lk" href="">и ещё 3 товара</a></li>
                    </ul>
                </div>

                <div class="orderLn_c">
                    <div>Самовывоз 11 июл. 2014 9:00…18:00</div>
                    <div>Оплата при получении: наличные, банковская карта</div>
                </div>

                <div class="orderLn_r">
                    <div class="orderLn_row orderLn_row-summ">
                        <span class="summT">Сумма заказа:</span>
                        <span class="summP">42 740 <span class="rubl">p</span></span>
                    </div>

                    <div class="orderLn_box orderLn_box-pay">
                        <div class="orderLn_row orderLn_row-pay">
                            <div class="payT">Можно <span class="payBtn btn4"><span class="brb-dt">оплатить онлайн</span></span></div>

                            <img width="25" src="/styles/order/img/master.png" alt="" />
                            <img width="25" src="/styles/order/img/Visa.png" alt="" />
                            <img width="43" src="/styles/order/img/paypal.png" alt="" />
                            <img width="16" src="/styles/order/img/psb.png" alt="" />
                        </div>

                        <ul style="display: none;" class="customSel_lst popupFl">
                            <li class="customSel_i">
                                <strong>PayPal</strong><br/>
                                Вы можете оплатить ваш заказ прямо сейчас с помощью PayPal.
                            </li>

                            <li class="customSel_i">
                                <strong>Онлайн оплата</strong><br/>
                                Вы можете оплатить ваш заказ прямо сейчас. К оплате принимаются банковские карты платежных систем Visa, MasterCard, Diners Club, JCB. Услуга бесплатная, никаких дополнительных процентов вы не платите.
                            </li>

                            <li class="customSel_i">
                                <strong>Выставить электронный счёт в личный кабинет Промсвязьбанка</strong><br/>
                                Вы можете оплатить Ваш заказ путём выставления счёта в Ваш личный кабинет. Данная услуга доступна только для клиентов Промсвязьбанка.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="orderLn clearfix">
                <div class="orderLn_l">
                    <div class="orderLn_row orderLn_row-t"><strong>Заказ</strong> <a href="">COXD-305176</a></div>

                    <ul class="orderLn_lst">
                        <li class="orderLn_lst_i">Бумажный конструктор 3 шт.</li>
                        <li class="orderLn_lst_i">Батарейки AAAA 18 шт.</li>
                        <li class="orderLn_lst_i"><a class="orderLn_lst_lk" href="">и ещё 3 товара</a></li>
                    </ul>
                </div>

                <div class="orderLn_c">
                    <div>Самовывоз 11 июл. 2014 9:00…18:00</div>
                    <div>Оплата при получении: наличные, банковская карта</div>
                </div>

                <div class="orderLn_r">
                    <div class="orderLn_row orderLn_row-summ">
                        <span class="summT">Сумма заказа:</span>
                        <span class="summP">42 740 <span class="rubl">p</span></span>
                    </div>

                    <div class="orderLn_box orderLn_box-pay">
                        <div class="orderLn_row orderLn_row-pay">
                            <div class="payT">Можно <span class="payBtn btn4"><span class="brb-dt">оплатить онлайн</span></span></div>

                            <img width="25" src="/styles/order/img/master.png" alt="" />
                            <img width="25" src="/styles/order/img/Visa.png" alt="" />
                            <img width="43" src="/styles/order/img/paypal.png" alt="" />
                            <img width="16" src="/styles/order/img/psb.png" alt="" />
                        </div>

                        <ul style="display: none;" class="customSel_lst popupFl">
                            <li class="customSel_i">
                                <strong>PayPal</strong><br/>
                                Вы можете оплатить ваш заказ прямо сейчас с помощью PayPal.
                            </li>

                            <li class="customSel_i">
                                <strong>Онлайн оплата</strong><br/>
                                Вы можете оплатить ваш заказ прямо сейчас. К оплате принимаются банковские карты платежных систем Visa, MasterCard, Diners Club, JCB. Услуга бесплатная, никаких дополнительных процентов вы не платите.
                            </li>

                            <li class="customSel_i">
                                <strong>Выставить электронный счёт в личный кабинет Промсвязьбанка</strong><br/>
                                Вы можете оплатить Ваш заказ путём выставления счёта в Ваш личный кабинет. Данная услуга доступна только для клиентов Промсвязьбанка.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="orderCompl clearfix">
            <button class="orderCompl_btn m-auto btnsubmit">Продолжить покупки</button>
        </div>
    </section>

<? };

