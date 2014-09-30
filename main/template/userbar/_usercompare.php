<?php
/** @var $page \View\DefaultLayout */
?>

<!-- добавляем, если добавили к сравнению товар topbarfix_cmpr-full -->
<div class="topbarfix_cmpr" data-bind="css: { 'topbarfix_cmpr-full': compare().length > 0 }">
    <a href="<?= \App::router()->generate('compare')?>" class="topbarfix_cmpr_tl">Сравнение</a>
    <span class="topbarfix_cmpr_qn" style="display: none" data-bind="visible: compare().length > 0 , text: compare().length"></span>
	
	<!-- что бы показать окно добавляем класс topbarfix_cmpr_popup-show -->
	<!-- скрывать при windows.scroll() и при наведении на любой элемент шапки или фиксбара-параплашки -->
    <div class="topbarfix_cmpr_popup topbarfix_cmpr_popup-show js-compare-popup">
    	<div class="topbarfix_cmpr_popup_inn">
    		<div class="clsr2 js-compare-popup-closer"></div>
	    	<strong>Товаров для сравнения пока нет.</strong>
		    <p><span style="display: inline-block;">Добавляйте товары к сравнению кнопкой</span> <span class="btnCmprb"></span></p>
    	</div>
    </div>
</div>