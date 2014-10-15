<?php

return function(
    \Helper\TemplateHelper $helper
) {
?>

    <div style="display: none" class="jsRegion" data-value="<?= \App::user()->getRegion() ? \App::user()->getRegion()->getName() : '' ?>"></div>

    <!-- шапка оформления заказа -->
    <header class="orderHd">
        <img class="orderHd_lg" src="/styles/order/img/logo.png" />

        <ul class="orderHd_stps">
            <li class="orderHd_stps_i" style="color: #fff;">Оформление заказа</li>
        </ul>
    </header>
    <!-- /шапка оформления заказа -->

    <section class="orderLgift">
        <div class="orderLgift_hd">
            <h1 class="orderLgift_t">ВЫ ОФОРМЛЯЕТЕ ЗАКАЗ В ПОДАРОК РЕБЕНКУ, КОТОРОГО ПОДДЕРЖИВАЕТ ФОНД "ПОДАРИ ЖИЗНЬ".</h1>
            <p class="orderLgift_slgn">ОПЛАТИТЕ ЗАКАЗ ОНЛАЙН, И ENTER ДОСТАВИТ ПОДАРОК РЕБЕНКУ К НОВОМУ ГОДУ.</p>
        </div>
        
        <form action="" class="orderU clearfix">
            
            <fieldset class="orderU_flds">
                <legend class="orderLgift_st">Подарок</legend>

                <div class="orderLgift_prod">
                    <img class="orderLgift_img" src="http://fs08.enter.ru/1/1/200/83/223272.jpg" alt="">

                    <div class="orderLgift_dscr">
                        <div class="orderLgift_dscr_n">Конструктор</div>
                        <div class="orderLgift_dscr_n">LEGO City 60017 Эвакуатор</div>
                        <div class="orderLgift_dscr_pr">1 828 <span class="rubl">p</span></div>
                    </div>
                </div>

                <div class="orderAuth">
                    <div class="orderAuth_t">Уже заказывали у нас?</div>
                    <button class="orderAuth_btn btnLightGrey">Войти с паролем</button>
                </div>
            </fieldset>

            <fieldset class="orderU_flds clesrfix">
                <legend class="orderLgift_st">От кого</legend>
                
                <div class="fl-l">
                    <div class="orderU_fld">
                        <label class="orderU_lbl orderU_lbl-str" for="">Телефон</label>
                        <input class="orderU_tx textfield" type="text" name="" value="" placeholder="">
                    </div>

                    <div class="orderU_fld">
                        <label class="orderU_lbl" for="">E-mail</label>
                        <input class="orderU_tx textfield" type="text" name="" value="" placeholder="">
                    </div>

                    <div class="orderU_fld">
                        <label class="orderU_lbl" for="">Имя</label>
                        <input class="orderU_tx textfield" type="text" name="" value="" placeholder="">
                    </div>
                </div>

                <div class="orderU_txbox">
                    <label for="" class="orderU_lbtx">Добрые пожелания ребёнку</label>
                    <textarea name="" id="" class="orderU_txarea"></textarea>
                </div>
            </fieldset>

            <fieldset class="orderU_flds clesrfix">
                <legend class="orderLgift_st">Оплатить онлайн</legend>
                
                <p class="orderLgift_paydscr">
                    <strong>Просим вас сразу оплатить подарок.</strong><br/>
                    Детям передаются только оплаченные товары. 
                </p>

                <ul class="onpay_lst">
                    <li class="onpay_lst_i">
                        <input type="radio" id="pay1" name="paynow" class="jsCustomRadio customInput customInput-defradio" checked>
                        <label class="customLabel customLabel-defradio" for="pay1">
                            <span class="customLabel_tx">Банковская карта</span> 
                            <img class="customLabel_img" src="/styles/order/img/icon_visa.png" alt=""> 
                            <img class="customLabel_img" src="/styles/order/img/icon_mc.png" alt="">
                        </label>
                        <p class="onpay_lst_desc">А также Maestro, Diners Club, JCB.</p>
                    </li>

                    <li class="onpay_lst_i">
                        <input type="radio" id="pay2" name="paynow" class="jsCustomRadio customInput customInput-defradio">
                        <label class="customLabel customLabel-defradio" for="pay2">
                            <span class="customLabel_tx">Платёжная система</span> 
                            <img class="customLabel_img" src="/styles/order/img/icon_pp.png" alt="">
                        </label>
                    </li>
                </ul>

                <p class="onpay_footn">Вы будете автоматически перемещены на сайт платежной системы.</p>
            </fieldset>

            <fieldset class="orderCompl clearfix">
                <div class="orderCompl_l orderCompl_l-ln orderCheck orderCheck-str">
                    <input type="checkbox" class="jsCustomRadio customInput customInput-checkbox" id="accept" name="" value="" />

                    <label  class="customLabel customLabel-checkbox" for="accept">
                        Я ознакомлен и согласен с информацией о продавце и его офертой
                    </label>
                </div>

                <button class="orderCompl_btn btnsubmit">Оформить</button>
            </fieldset>
        </form>
    </section>

<? } ?>