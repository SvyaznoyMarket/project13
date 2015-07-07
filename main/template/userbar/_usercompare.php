<?php
/** @var $page \View\DefaultLayout */
?>

<li class="userbtn_i topbarfix_cmpr" data-bind="css: { 'topbarfix_cmpr-full': compare().length > 0 }">
    <i class="i-header i-header--compare"></i>
    <a href="<?= \App::router()->generate('compare')?>" class="topbarfix_cmpr_tl" data-bind="visible: compare().length > 0" style="display: none">Сравнение</a>
    <span class="topbarfix_cmpr_tl js-noProductsForCompareLink" data-bind="visible: compare().length == 0">Сравнение</span>
    <span class="topbarfix_cmpr_qn" style="display: none" data-bind="visible: compare().length > 0, text: compare().length"></span>

    <div class="topbarfix_cmpr_popup js-compare-popup">
    	<div class="topbarfix_cmpr_popup_inn">
    		<div class="clsr2 js-compare-popup-closer"></div>
	    	<strong>Товаров для сравнения пока нет</strong>
		    <p><span style="display: inline-block; vertical-align: middle;">Добавляйте товары к сравнению кнопкой</span> <span class="btnCmprb"></span></p>
    	</div>
    </div>

    <div class="topbarfix_cmpr_popup topbarfix_cmpr_popup-add js-compare-addPopup">
        <div class="topbarfix_cmpr_popup_inn">
            <div class="clsr2 js-compare-addPopup-closer"></div>
            <strong>Товар добавлен к сравнению</strong>
            <div class="cmprAdd">
                <img src="" width="40" height="40" alt="" class="cmprAdd_img js-compare-addPopup-image" />

                <div class="cmprAdd_n">
                    <span class="cmprAdd_n_t js-compare-addPopup-prefix"></span><br/>
                    <span class="js-compare-addPopup-webName"></span>
                </div>
            </div>
        </div>
    </div>
</li>