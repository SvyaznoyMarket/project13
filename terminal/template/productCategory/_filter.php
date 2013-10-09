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
<article data-pagetype="filter">
    <!-- Filter -->
    <form class="bFilter bigfilter form bSpec" action="" method="get">
    	<!-- <div class="filterresult product_count-block">
    		<div class="corner"><div></div></div>
    		Выбрано <span class="result">result.data</span> модел<span class="ending">ending</span><br/>
    		<a>Показать</a>
    	</div> -->
        <dl class="clearfix">

            <? $openNum = 0 ?>
            <? $index = 0; foreach ($productFilter->getFilterCollection() as $filter): ?>
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
            <? endforeach ?>

            <dt class="bFilter__eClear pb10"><input type="button" class="bFilter__eClearBtn bButton mSmallGrayBtn" value="Очистить"/></dt>
            <dt class="bFilter__eSubmit pb10"><input type="submit" class="bFilter__eSubmitBtn bButton mSmallOrangeBtn" value="Подобрать"/></dt>
        </dl>
    </form>
    <!-- /Filter -->
</article>