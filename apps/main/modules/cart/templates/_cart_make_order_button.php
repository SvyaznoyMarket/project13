<div class="fl width345 font14">
	<div id="creditFlag" style="display:none">
	<label class="bigcheck <?php if ($selectCredit) echo 'checked'; ?>" for="selectCredit">
		<b></b>Выбранные товары купить в кредит
		<input autocomplete="off" class="" type="checkbox" id="selectCredit" <?php if ($selectCredit) echo 'checked="checked"'; ?>
			name="selectCredit"/>
	</label>
	
	<div class="pl35 font11">
		 Получение кредита происходит онлайн после оформления заказа на сайте: заполняете заявку на кредит в банк,
		 и в течение нескольких минут получаете СМС о решении банка. Оригиналы документов мы привезем вместе с выбранными товарами!
    </div>
    </div>
    
</div>
<div id="total" class="fr">
    <div class="left">
    	
    	<!--div id="creditSum" data-minsum="<?php echo ProductEntity::MIN_CREDIT_PRICE ?>" style="display:none">
			<div class="font14">
				Сумма заказа:
                <span>
                    <span class="price"><?php echo $cart->getTotal(true) ?></span> <span class="rubl">p</span>
                    <br/>Сумма ежемесячного платежа
                <span>        
			</div>
			<div class="font30"><strong>
				<span id="creditPrice">(считаем...)</span>
        		<span class="rubl">p</span>
        	</strong></div>
	    </div-->

	    <div id="creditSum" data-minsum="<?php echo ProductEntity::MIN_CREDIT_PRICE ?>" style="display:none">
			<div class="font14 width370 creditInfo pb10 grayUnderline">
				<div class="leftTitle">Сумма заказа:</div>
                <div class="font24">
                    <span class="price"><?php echo $cart->getTotal(true) ?></span> <span class="rubl">p</span>
                </div>
			</div>
			<div class="font14 width370 creditInfo pb10 pt10">
				<div class="leftTitle">
					<strong>Ежемесячный платеж<sup>*</sup>:</strong>
				</div>
				<div class="font24">
					<strong>
						<span id="creditPrice">(считаем...)</span>
		        		<span class="rubl">p</span>
		        	</strong>
		        </div>
        	</div>
        	<div class="font11 width370"><sup>*</sup> Кредит не распространяется на услуги F1 и доставку. 
        	Сумма платежей предварительная и уточняется банком в процессе принятия кредитного решения.</div>
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