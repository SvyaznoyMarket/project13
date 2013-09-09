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

$from = $page->helper->clearZeroValue($productFilter->getValueMin($filter));
$to = $page->helper->clearZeroValue($productFilter->getValueMax($filter));

$min_from = $page->helper->clearZeroValue($filter->getMin());
$max_to = $page->helper->clearZeroValue($filter->getMax());

if ($from > $min_from) $from = round($from,1);
if ($to< $max_to) $to = round($to,1);
?>

<dt<? if (5 > $index) { ?> class="<?= ((1 == $index) ? ' first' : '') ?>"<? } ?>>
    <?= $filter->getName() ?>
</dt>

<dd style="display: <?= $isOpened ? 'block' : 'none' ?>;">
    <div class="bSlide">
        <input type="hidden" name="<?= $formName ?>[<?= $filter->getId() ?>][from]" value="<?= $from ?>" id="f_<?= $filter->getId()?>_from"/>
        <input type="hidden" name="<?= $formName ?>[<?= $filter->getId() ?>][to]" value="<?= $to ?>" id="f_<?= $filter->getId()?>_to"/>

        <div class="sliderbox">
            <div id="slider-<?= uniqid()?>" class="filter-range"></div>
        </div>
        <div class="pb5">
            <input class="slider-from" type="hidden" disabled="disabled" value="<?= $min_from ?>"/>
            <input class="slider-to" type="hidden" disabled="disabled" value="<?= $max_to ?>"/>
            <span class="slider-interval"  <?php if ($filter->getStepType()): ?>  data-step="<?= $page->json($filter->getStepByFilter()) ?>" <?php endif ?>  ></span>

            <?if ($filter->getId() == 'price') { ?>
                <span class="rubl">p</span>
            <? } else { ?>
                <?= $filter->getUnit() ?>
            <? } ?>
        </div>
    </div>
    <div class="clear"></div>
</dd>