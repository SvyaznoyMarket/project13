<?php

use Model\Product\Filter\Entity as FilterEntity;

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
$values = $productFilter->getValue($filter);

$isOpened = false;
foreach ($filter->getOption() as $option) {
    if (in_array($option->getId(), $values)) {
        $isOpened = true;
        break;
    }
}
?>

<dt class="bFilter__eName <? if (5 > $index) { ?> <?= ((1 == $index) ? ' first' : '') ?><? } ?>">
    <?= $filter->getName() ?>
</dt>

<dd class="bFilter__eProp"<? if ($isOpened): ?> style="display: block"<? endif ?>>
    <ul class="checkbox_list clearfix">
        <? $i = 0; foreach ($filter->getOption() as $option) { $id = $option->getId() ?>
        <li>
            <input name="<?= $formName ?>[<?= $filter->getId()?>][]" type="checkbox" value="<?= $id ?>"<? if (in_array($id, $values)) { ?> checked="checked"<? } ?> id="<?= $formName ?>_<?= $filter->getId() ?>_<?= $id ?>" class="hiddenCheckbox" />
            <label for="<?= $formName ?>_<?= $filter->getId() ?>_<?= $id?>" class="prettyCheckbox checkbox list">
                <span class="holderWrap" style="width: 13px; height: 13px; ">
                    <span class="holder" style="width: 13px; "></span>
                </span>
                <?= $option->getName() ?>
            </label>
        </li>
        <? $i++; } ?>
    </ul>
</dd>

<hr class="bFilter__eHR"/>