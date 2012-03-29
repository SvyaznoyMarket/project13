<?php
/**
 * @var $productFilter ProductCoreFormFilterSimple
 * @var $filter ProductCategoryFilterEntity|sfOutputEscaperObjectDecorator
 * @var $i int
 * @var $open string
 */
?>

<dt<?php if (5 > $i) echo ' class="' . ((1 == $i) ? ' first' : '') . '"' ?>>
  <?php echo $filter->getName() ?>
</dt>

<dd style="display: <?php echo $open ?>;">
  <div class="bSlide">
    <input type="hidden" name="<?php echo $productFilter->getName() ?>[<?php echo $filter->getFilterId()?>][from]"
           value="<?php echo myToolkit::clearZero($productFilter->getValueMin($filter->getRawValue()))?>"
           id="f_<?php echo $filter->getFilterId()?>_from"/>
    <input type="hidden" name="<?php echo $productFilter->getName() ?>[<?php echo $filter->getFilterId()?>][to]"
           value="<?php echo myToolkit::clearZero($productFilter->getValueMax($filter->getRawValue()))?>"
           id="f_<?php echo $filter->getFilterId()?>_to"/>

    <div class="sliderbox">
      <div id="slider-<?php echo uniqid()?>" class="filter-range"></div>
    </div>
    <div class="pb5">
      <input class="slider-from" type="hidden" disabled="disabled"
             value="<?php echo myToolkit::clearZero($filter->getMin())?>"/>
      <input class="slider-to" type="hidden" disabled="disabled"
             value="<?php echo myToolkit::clearZero($filter->getMax())?>"/>
      <span class="slider-interval"></span>
      <?php if ($filter->getFilterId() == 'price') { ?>
      <span class="rubl">p</span>
      <?php } else { ?>
      <?php echo $filter->getUnit() ?>
      <?php } ?>
    </div>
  </div>
  <div class="clear"></div>
</dd>