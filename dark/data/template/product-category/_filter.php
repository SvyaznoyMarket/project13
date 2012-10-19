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
<form class="product_filter-block" action="" method="get" data-action-count="<?= $page->url('product.category.count', array('categoryPath' => $category->getPath())) ?>">
	<div class="filterresult product_count-block">
		<div class="corner"><div></div></div>
		Выбрано <span class="result">result.data</span> модел<span class="ending">ending</span><br/>
		<a>Показать</a>
	</div>
    <dl class="bigfilter form bSpec">
        <dt class="filterHeader">Выбираем:<i></i></dt>
        <? require __DIR__ . '/_selectedFilter.php' ?>

        <? $openNum = 0 ?>
        <? $index = 0; foreach ($productFilter->getFilterCollection() as $filter) { ?>
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
        <? } ?>

        <div class="pb10"><input type="submit" class="button yellowbutton" value="Подобрать"/></div>
    </dl>
</form>
<!-- /Filter -->