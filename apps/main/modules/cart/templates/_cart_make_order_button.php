<script src="http://direct-credit.ru/JsHttpRequest.js" type="text/javascript"></script>
<script src="http://direct-credit.ru/widget/dc_script_utf.js" type="text/javascript"></script>


<div class="fl width645 font14">
    <?php if ($cart->getTotal() >= ProductEntity::MIN_CREDIT_PRICE ) : ?>
        <label class="bigcheck" for=""><b></b>Выбранные товары купить в кредит<input class="<?php if ($selectCredit) echo 'selected'; ?>" type="radio" value="1" name=""></label>

        <input class="dc_credit_button" id="dc_buy_on_credit_tmp" name="dc_buy_on_credit" type="button" value="Расчет платежа..." />
        <script type="text/javascript">dc_getCreditForTheProduct('4427', '<?php echo session_id();?>', 'getValueOfMonthlyPayment', { price : '<?php echo $cart->getTotal() ?>', count : '1', name : 'tmp', product_type : 'another', articul : 'tmp', button_id : '', cart : '/cart' });</script>

    <?php endif; ?>
    <div class="pl35">
        Мы привезем заказ по любому удобному вам адресу. Пожалуйста, укажите дату и время доставки.<br><br>
        Для оформления заказа от вас потребуется только имя и телефон для связи.
    </div>
</div>
<div class="fr ar">
    <div class="left">
        <div class="font14">
            Сумма заказа:
            <?php if ($cart->getTotal() >= ProductEntity::MIN_CREDIT_PRICE) : ?>
                <span  <?php if (!$selectCredit) : ?> style="display: none;" <?php endif; ?>>
                    <span class="price"><?php echo $cart->getTotal(true) ?></span> <span class="rubl">p</span>
                    <br>Сумма первоночального взноса
                <span>
            <?php endif; ?>
        </div>
        <div class="font30"><strong><span class="price"><?php echo $cart->getTotal(true) ?></span> <span class="rubl">p</span></strong></div>
    </div>

</div>
<div class="clear pb25"></div>
<div class="line pb30"></div>
<div class="fl font14 pt10">&lt; <a class="underline" href="/">Вернуться к покупкам</a></div>
<div class="width500 auto">
    <a href="<?php echo url_for('order_new') ?>" class="bBigOrangeButton width345">Оформить заказ</a>
</div>
<div class="clear"></div>