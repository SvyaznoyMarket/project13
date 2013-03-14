<?php
/**
 * @var $page            \View\Layout
 * @var $title           string|null
 * @var $breadcrumbs     array('url' => null, 'name' => null)[]
 * @var $hasSearch       bool
 * @var $hasSeparateLine bool
 * @var $extendedMargin  bool
 */
?>

<?
$hasSearch = isset($hasSearch) ? (bool)$hasSearch : true;
$hasSeparateLine = isset($hasSeparateLine) ? (bool)$hasSeparateLine : false;
$extendedMargin = isset($extendedMargin) ? (bool)$extendedMargin : false;
?>

<div class="pagehead">

    <? if ($hasSearch): ?>
    <noindex>
        <div class="searchbox">
            <?= $page->render('search/form-default') ?>
            <div class="bSearchSuggest">
                <div class="bSearchSuggest__eCategoryList">
                    <p class="bSearchSuggest__eListLine"><span class="bSearchSuggest__eListTitle">Категория</span></p>
                    <a class="bSearchSuggest__eCategoryRes">
                        <span class="bSearchSuggest__eSelected">Нож</span>и
                    </a>
                    <a class="bSearchSuggest__eCategoryRes" href="#">
                        <span class="bSearchSuggest__eSelected">Нож</span>и-инструменты
                    </a>
                    <a class="bSearchSuggest__eCategoryRes" href="#">
                        Наборы <span class="bSearchSuggest__eSelected">нож</span>ей
                    </a>
                    <a class="bSearchSuggest__eCategoryRes" href="#" href="#">
                        Точилки и подставки для <span class="bSearchSuggest__eSelected">нож</span>ей
                    </a>
                    <a class="bSearchSuggest__eCategoryRes" href="#">
                        <span class="bSearchSuggest__eSelected">Нож</span>ницы и <span class="bSearchSuggest__eSelected">нож</span>и
                    </a>
                </div>
                <div class="bSearchSuggest__eProductList">
                    <p class="bSearchSuggest__eListLine"><span class="bSearchSuggest__eListTitle">Товар</span></p>
                    <a class="bSearchSuggest__eGoodRes clearfix" href="#">
                        <img class="bSearchSuggest__eGoodImgRes fl" src="http://fs06.enter.ru/1/1/163/3a/35965.jpg" width="48" height="48"/>
                        <p class="bSearchSuggest__eGoodTitleRes fl">Столовый <span class="bSearchSuggest__eSelected">Нож</span> 12см Arcos</p>
                    </a>
                    <a class="bSearchSuggest__eGoodRes clearfix" href="#">
                        <img class="bSearchSuggest__eGoodImgRes fl" src="http://fs06.enter.ru/1/1/163/3a/35965.jpg" width="48" height="48"/>
                        <p class="bSearchSuggest__eGoodTitleRes fl">Столовый <span class="bSearchSuggest__eSelected">Нож</span> 12см Arcos</p>
                    </a>
                    <a class="bSearchSuggest__eGoodRes clearfix" href="#">
                        <img class="bSearchSuggest__eGoodImgRes fl" src="http://fs06.enter.ru/1/1/163/3a/35965.jpg" width="48" height="48"/>
                        <p class="bSearchSuggest__eGoodTitleRes fl">Столовый <span class="bSearchSuggest__eSelected">Нож</span> 12см Arcos</p>
                    </a>
                </div>
            </div>
        </div>
    </noindex>
    <? endif ?>

    <?php echo $page->render('_breadcrumbs', array('breadcrumbs' => $breadcrumbs, 'class' => 'breadcrumbs')) ?>

    <div class="clear"></div>

    <? if ($title): ?><h1><?= $title ?></h1><? endif ?>

    <div class="clear<? if ($extendedMargin): ?> pb20<? endif ?>"></div>
    <? if ($hasSeparateLine): ?>
    <div class="line"></div>
    <? endif ?>
</div>
