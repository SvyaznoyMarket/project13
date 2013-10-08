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

$helper = &$page->helper;


$from = $productFilter->getValueMin($filter);
$to = $productFilter->getValueMax($filter);
$min_from = $filter->getMin();
$max_to = $filter->getMax();


$from = $helper->roundToOneDecimal($from);
$to = $helper->roundToOneDecimal($to, true);
$min_from = $helper->roundToOneDecimal($min_from);
$max_to = $helper->roundToOneDecimal($max_to, true);


$from = $helper->clearZeroValue( $from );
$to = $helper->clearZeroValue( $to );
$min_from = $helper->clearZeroValue( $min_from );
$max_to = $helper->clearZeroValue( $max_to );

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