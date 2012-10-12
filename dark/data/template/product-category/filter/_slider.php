<?php
/**
 * @var $page          \View\DefaultLayout
 * @var $category      \Model\Product\Category\Entity
 * @var $productFilter \Model\Product\Filter
 * @var $filter        \Model\Product\Filter\Entity
 * @var $isOpened      bool
 * @var $index         int
 */
?>

<dt<? if (5 > $index) { ?> class="<?= ((1 == $index) ? ' first' : '') ?>"<? } ?>>
    <?= $filter->getName() ?>
</dt>

<dd style="display: <?= $isOpened ? 'block' : 'none' ?>;">
    <div class="bSlide">
        <input type="hidden" name="<?= $name ?>[<?= $filter->getFilterId() ?>][from]" value="<?= $page->helper->clearZeroValue($productFilter->getValueMin($filter)) ?>" id="f_<?= $filter->getFilterId()?>_from"/>
        <input type="hidden" name="<?= $name ?>[<?= $filter->getFilterId() ?>][to]" value="<?= $page->helper->clearZeroValue($productFilter->getValueMax($filter)) ?>" id="f_<?= $filter->getFilterId()?>_to"/>

        <div class="sliderbox">
            <div id="slider-<?= uniqid()?>" class="filter-range"></div>
        </div>
        <div class="pb5">
            <input class="slider-from" type="hidden" disabled="disabled" value="<?= $page->helper->clearZeroValue($filter->getMin())?>"/>
            <input class="slider-to" type="hidden" disabled="disabled" value="<?= $page->helper->clearZeroValue($filter->getMax())?>"/>
            <span class="slider-interval"></span>

            <?if ($filter->getFilterId() == 'price') { ?>
                <span class="rubl">p</span>
            <? } else { ?>
                <?= $filter->getUnit() ?>
            <? } ?>
        </div>
    </div>
    <div class="clear"></div>
</dd>