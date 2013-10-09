<?php
/**
 * @var $page          \View\Layout
 * @var $category      \Model\Product\Category\Entity
 * @var $productFilter \Model\Product\Filter
 * @var $filter        \Model\Product\Filter\Entity
 * @var $isOpened      bool
 * @var $index         int
 * @var $formName      string
 */
?>

<?
$isOpened =
    ($productFilter->getValueMin($filter) != $filter->getMin())
    || ($productFilter->getValueMax($filter) != $filter->getMax())
    ;
?>

<dt class="bFilter__eName <? if (5 > $index) { ?> <?= ((1 == $index) ? ' first' : '') ?><? } ?>">
    <?= $filter->getName() ?>
</dt>

<dd class="bFilter__eProp"<? if ($isOpened): ?> style="display: block"<? endif ?>>
    <div class="bSlide">
        <input type="hidden" name="<?= $formName ?>[<?= $filter->getId() ?>][from]" value="<?= $page->helper->clearZeroValue($productFilter->getValueMin($filter)) ?>" id="f_<?= $filter->getId()?>_from"/>
        <input type="hidden" name="<?= $formName ?>[<?= $filter->getId() ?>][to]" value="<?= $page->helper->clearZeroValue($productFilter->getValueMax($filter)) ?>" id="f_<?= $filter->getId()?>_to"/>
        <div class="pb5">
            <input class="slider-from" type="hidden" disabled="disabled" value="<?= $page->helper->clearZeroValue($filter->getMin())?>"/>
            <input class="slider-to" type="hidden" disabled="disabled" value="<?= $page->helper->clearZeroValue($filter->getMax())?>"/>
            <span class="slider-interval"></span>

            <?if ($filter->getId() == 'price') { ?>
                <span class="bRuble">p</span>
            <? } else { ?>
                <span><?= $filter->getUnit() ?></span>
            <? } ?>
        </div>
        <div class="sliderbox">
            <div id="slider-<?= uniqid()?>" class="filter-range"></div>
        </div>
        
    </div>
    <div class="clear"></div>
</dd>

<hr class="bFilter__eHR"/>