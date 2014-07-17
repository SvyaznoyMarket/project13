<?php

return function(
    \Helper\TemplateHelper $helper
) {

?>

<?= $helper->render('order-v3/__head', ['step' => 1]) ?>

<section class="orderCnt">
    <h1 class="orderCnt_t">Оформление заказа</h1>

    <form id="js-orderForm" action="<?= $helper->url('orderV3.update.contact') ?>" class="orderU" method="" accept-charset="utf-8">
        <fieldset class="orderU_flds">
            <div class="orderU_fld">
                <label class="orderU_lbl orderU_lbl-str" for="">Телефон</label>
                <input class="orderU_tx textfield" type="text" name="order[phone]" value="" placeholder="">
                <span class="orderU_hint">Для смс о состоянии заказа</span>
            </div>

            <div class="orderU_fld">
                <label class="orderU_lbl" for="">E-mail</label>
                <input class="orderU_tx textfield" type="text" name="" value="" placeholder="">
            </div>

            <div class="orderU_fld">
                <label class="orderU_lbl" for="">Имя</label>
                <input class="orderU_tx textfield" type="text" name="" value="" placeholder="">
                <span class="orderU_hint">Как к вам обращаться?</span>
            </div>
        </fieldset>

        <fieldset class="orderU_flds">
            <span class="orderU_bonus">Начислить баллы</span>

            <img class="orderU_bonusImg" src="/styles/order/img/sClub.png" alt="" />
            <img class="orderU_bonusImg" src="/styles/order/img/sBank.png" alt="" />
        </fieldset>
    </form>

    <div class="orderAuth">
        <div class="orderAuth_t">Уже заказывали у нас?</div>
        <button class="orderAuth_btn btnLightGrey">Войти с паролем</button>
    </div>

    <div class="orderCompl clearfix">
        <div class="orderCompl_l orderCompl_l-ln orderCheck orderCheck-str mb10">
            <input type="checkbox" class="customInput customInput-checkbox" id="accept" name="" value="" />

            <label  class="customLabel" for="accept">
                Я ознакомлен и согласен с «Условиями продажи» и «Правовой информацией»
            </label>
        </div>

        <button form="js-orderForm" class="orderCompl_btn btnsubmit" type="submit">Далее ➜</button>
    </div>
</section>

<?
};

