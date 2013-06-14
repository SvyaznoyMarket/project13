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
<form class="product_filter-block" action="<?= 'product.category.brand' == \App::request()->attributes->get('route') ? $page->url('product.category', ['categoryPath' => $category->getPath()]) : '' ?>" method="get" data-action-count="<?= $page->url('product.category.count', array('categoryPath' => $category->getPath())) ?>">
	<div class="filterresult product_count-block">
		<div class="corner"><div></div></div>
		Выбрано <span class="result">result.data</span> модел<span class="ending">ending</span><br/>
		<a>Показать</a>
	</div>
    <dl class="bigfilter form bSpec">
        <dt class="filterHeader">Выбираем:<i></i></dt>
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