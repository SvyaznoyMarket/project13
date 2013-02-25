<?php
/**
 * @var $page \View\Layout
 * @var $user \Session\User
 */
?>
<div class="clear"></div>
    <div class="bF1SaleCard">	
		<div class="pl35">
            <h3 class="bF1SaleCard_eTitle ">Скидка по карте «Под защитой F1»</h3>
            <? if ((bool)$user->getCart()->getCertificates()): ?>
		    <? // TODO: верстка примененного сертификата ?>
			<div class="bF1SaleCard_eComplete mGold">
				<p class="font18">Ваша скидка на услуги F1 — <b>10%, 90р</b></p>
			</div>
			<? else: ?>
			<div class="bF1SaleCard_eForm">
	            <p class="font11">Введите серийный номер карты «Под защитой F1» для скидки на услуги:</p>
	            <input class="mr20 width370" type="text"/><input class="yellowbutton button" type="button" value="Получить скидку"/>
	        </div>
	         <? endif ?>
        </div>
        <div class="line mt32 pb30"></div>
    </div>