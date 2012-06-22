<div class="fl width645 font14">
	<div id="creditFlag" style="display:none">
	<label class="bigcheck <?php if ($selectCredit) echo 'checked'; ?>" for="selectCredit">
		<b></b>Выбранные товары купить в кредит
		<input class="" type="checkbox" id="selectCredit" <?php if ($selectCredit) echo 'checked="checked"'; ?>
			value="<?php if ($selectCredit){ echo '1';} else { echo '0'; } ?>" name="selectCredit"/>
	</label>
	</div>
	<div class="pl35">
		Мы привезем заказ по любому удобному вам адресу. Пожалуйста, укажите дату и время доставки.<br><br>
		Для оформления заказа от вас потребуется только имя и телефон для связи.
    </div>
    
</div>
<div id="total" class="fr ar">
    <div class="left">
    	
    	<div id="creditSum" data-minsum="<?php echo ProductEntity::MIN_CREDIT_PRICE ?>" style="display:none">
			<div class="font14">
				Сумма заказа:
                <span>
                    <span class="price"><?php echo $cart->getTotal(true) ?></span> <span class="rubl">p</span>
                    <br/>Сумма первоночального взноса
                <span>        
			</div>
			<div class="font30"><strong>
				<span id="creditPrice">(считаем...)</span>
        		<span class="rubl">p</span>
        	</strong></div>
	    </div>

    	<div id="commonSum">
			<div class="font14">
				Сумма заказа:
			</div>
			<div class="font30"><strong>
				<span class="price">
						<?php echo $cart->getTotal(true); ?>
				</span>
			<span class="rubl">p</span></strong></div>
        </div>
    </div>

</div>
<div class="clear pb25"></div>
<div class="line pb30"></div>
<div class="fl font14 pt10">&lt; <a class="underline" href="/">Вернуться к покупкам</a></div>
<div class="width500 auto">
    <a href="<?php echo url_for('order_new') ?>" class="bBigOrangeButton width345">Оформить заказ</a>
</div>
<div class="clear"></div>