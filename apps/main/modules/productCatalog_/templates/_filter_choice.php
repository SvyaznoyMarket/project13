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
    <?php  ?>
    <?php foreach (array('нет', 'да') as $id => $name): $id = (int)$id; ?>
    <li>
      <input name="<?php echo $productFilter->getName() ?>[<?php echo $filter->getFilterId()?>][]"
             type="checkbox"
             value="<?php echo $id?>"
        <?php if (in_array($id, $values)) echo 'checked'; ?>
             id="<?php echo $productFilter->getName() ?>_<?php echo $filter->getFilterId()?>_<?php echo $id?>"
             class="hiddenCheckbox">
      <label for="<?php echo $productFilter->getName() ?>_<?php echo $filter->getFilterId()?>_<?php echo $id?>"
             class="prettyCheckbox checkbox list">
        <span class="holderWrap" style="width: 13px; height: 13px; "><span class="holder" style="width: 13px; "></span></span>
        <?php echo $name?>
      </label>
    </li>
    <?php endforeach; ?>
  </ul>
</dd>