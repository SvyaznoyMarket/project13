<?php
/**
 * @var $productFilter ProductCoreFormFilterSimple|sfOutputEscaperObjectDecorator
 * @var $productFilterRaw ProductCoreFormFilterSimple
 * @var $filter ProductCategoryFilterEntity|sfOutputEscaperObjectDecorator
 * @var $i int
 * @var $open string
 */

$productFilterRaw = $productFilter->getRawValue();
$values = $productFilterRaw->getValue($filter->getRawValue());
?>

<dt<?php if (5 > $i) echo ' class="' . ((1 == $i) ? ' first' : '') . '"' ?>>
  <?php echo $filter->getName() ?>
</dt>

<dd style="display: <?php echo $open ?>;">
  <ul class="checkbox_list">
    <?php $i = 0;?>
    <?php foreach ($filter->getOptionList() as $option): $id = (int)$option['id']; ?>
    <li <?php if ($i > 4) echo 'class="hf" style="display: none"'; ?> >
      <input name="<?php echo $productFilter->getName() ?>[<?php echo $filter->getFilterId()?>][]"
             type="checkbox"
             value="<?php echo $id?>"
        <?php if (in_array($id, $values)) echo 'checked'; ?>
             id="<?php echo $productFilter->getName() ?>_<?php echo $filter->getFilterId()?>_<?php echo $id?>"
             class="hiddenCheckbox">
      <label for="<?php echo $productFilter->getName() ?>_<?php echo $filter->getFilterId()?>_<?php echo $id?>"
             class="prettyCheckbox checkbox list">
        <span class="holderWrap" style="width: 13px; height: 13px; "><span class="holder" style="width: 13px; "></span></span>
        <?php echo $option['name']?>
      </label>
    </li>
    <?php $i++; endforeach; ?>
    <?php if ($i > 5) { ?>
    <li class="bCtg__eMore" style="padding-left: 10px;">
      <a href="#">еще...</a>
    </li>
    <?php }?>
  </ul>
</dd>