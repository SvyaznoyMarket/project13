<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\Order\CreatedEntity[] $orders
 */
$f = function(
    \Helper\TemplateHelper $helper,
    $orders
) {
?>

<? foreach ($orders as $order): ?>
<div class="orderOneClick">
    <h1 class="orderOneClick_t">Купить быстро в 1 клик</h1>

    <div class="orderCol_cnt clearfix">
        <div class="orderCol_lk">
            <img class="orderCol_img" src="http://fs01.enter.ru/1/1/60/3e/340440.jpg" alt="Смартфон Nokia Lumia 530 Dual Sim оранжевый">
        </div>

        <div class="orderCol_n">
            Смартфон<br>                    
            Nokia Lumia 530 Dual Sim оранжевый                
        </div>

        <span class="orderCol_data orderCol_data-price">4 990 <span class="rubl">p</span></span>
    </div>

    <div class="orderCol_f clearfix">
	    <div class="orderCol_f_r">
	        <span class="orderCol_summ">Бесплатно</span>
	        <span class="orderCol_summt">Самовывоз:</span>

	        <span class="orderCol_summ">4 990 <span class="rubl">p</span></span>
	        <span class="orderCol_summt">Итого:</span>
	    </div>
	</div>
	
	<div class="orderU_fldsbottom ta-c orderOneClick_cmpl">
    	<p class="orderOneClick_cmpl_t"><strong>Заявка</strong> <?= $order->getNumber() ?> <strong>оформлена!</strong></p>
    	<p style="margin-bottom: 20px;">Наш сотрудник позвонит Вам для уточнения деталей<br/> и зарегистрирует заказ.</p>
    	<a href="" class="orderCompl_btn btnsubmit">Продолжить покупки</a>
    </div>
</div>
<? endforeach ?>

<? }; return $f;
