<?php

use Model\Product\Filter\Entity as FilterEntity;

/**
 * @var $page          \View\Layout
 * @var $category      \Model\Product\Category\Entity
 * @var $productFilter \Model\Product\Filter
 */
?>

<?php
$formName = \View\Product\FilterForm::$name;
?>

<!-- Filter -->
<?
    if ('product.category.brand' == \App::request()->attributes->get('route') || \App::request()->get('shop')) {
        $link = $page->url('product.category', ['categoryPath' => $category->getPath()]);
    } else $link = '';

    $linkCount = $page->url('product.category.count', array('categoryPath' => $category->getPath()));
    //if (\App::request()->get('shop')) $linkCount .= (false === strpos($linkCount, '?') ? '?' : '&') . 'shop='. \App::request()->get('shop');

?>
<form class="product_filter-block" action="<?=$link?>" method="get" data-action-count="<?= $linkCount ?>">
	<div class="filterresult product_count-block">
		<div class="corner"><div></div></div>
		Выбрано <span class="result">result.data</span> модел<span class="ending">ending</span><br/>
		<a>Показать</a>
	</div>
    <dl class="bigfilter form bSpec<?= \App::config()->sphinx['showListingSearchBar'] ? ' noBorder' : '' ?>">
        <? if(\App::config()->sphinx['showListingSearchBar']) { ?>
            <div class="pb5">
                <input type="text" value="" class="text mb10 orangeIcon">
                <input type="hidden" value="" name="f[text]">
                <img class="mb10 orangeIcon" src="/css/search/img/searchBtn.png">
            </div>
        <? } else { ?>
            <dt class="filterHeader">Выбираем:<i></i></dt>
        <? } ?>
        <? require __DIR__ . '/_selectedFilter.php' ?>

        <? if ($category->getIsFurniture()): ?>
            <?= $page->tryRender('product-category/filter/_instore', ['category' => $category]) ?>
            <input type="hidden" name="instore" value="<?= $productFilter->inStore() ? 1 : '' ?>" />
        <? endif ?>

        <? $openNum = 0 ?>
        <? $index = 0; foreach ($productFilter->getFilterCollection() as $i => $filter): ?>
            <? if (($filter->getId() != 'label' && $i == 0) && $page->hasGlobalParam('shops')) {
                require __DIR__ . '/_shops.php';
            } ?>

            <? if (!$filter->getIsInList()) continue ?>
            <? if ('price' == $filter->getId() || 'brand' == $filter->getId()) {
                $isOpened = true;
            } elseif ($openNum < 5) {
                $openNum++;
                $isOpened = true;
            } else {
                $isOpened = false;
            } ?>

            <? switch ($filter->getTypeId()) {
                case FilterEntity::TYPE_NUMBER:
                case FilterEntity::TYPE_SLIDER:
                    require __DIR__ . '/filter/_slider.php';
                    break;
                case FilterEntity::TYPE_LIST:
                    require __DIR__ . '/filter/_list.php';
                    break;
                case FilterEntity::TYPE_BOOLEAN:
                    require __DIR__ . '/filter/_choice.php';
                    break;
            } ?>
            <?
            if ($filter->getId() == 'label' && $page->hasGlobalParam('shops')) {
                require __DIR__ . '/_shops.php';
            }
            ?>

        <? endforeach ?>

        <dt class="submit pb10"><input type="submit" class="button yellowbutton" value="Подобрать"/></dt>
    </dl>
</form>
<!-- /Filter -->