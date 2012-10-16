<?php

use Model\Product\Filter\Entity as FilterEntity;

/**
 * @var $page          \View\DefaultLayout
 * @var $category      \Model\Product\Category\Entity
 * @var $productFilter \Model\Product\Filter
 * @var $filter        \Model\Product\Filter\Entity
 * @var $isOpened      bool
 * @var $index         int
 * @var $formName      string
 */
?>

<?php $values = $productFilter->getValue($filter) ?>

<dt<? if (5 > $index) { ?> class="<?= ((1 == $index) ? ' first' : '') ?>"<? } ?>>
    <?= $filter->getName() ?>
</dt>

<dd style="display: <?= $isOpened ? 'block' : 'none' ?>;">
    <ul class="checkbox_list">
        <? $i = 0; foreach ($filter->getOption() as $option) { $id = $option->getId() ?>
        <li<? if ($i > 4) { ?> class="hf" style="display: none"<? } ?>>
            <input name="<?= $formName ?>[<?= $filter->getFilterId()?>][]" type="checkbox" value="<?= $id ?>"<? if (in_array($id, $values)) { ?> checked="checked"<? } ?> id="<?= $formName ?>_<?= $filter->getFilterId() ?>_<?= $id ?>" class="hiddenCheckbox" />
            <label for="<?= $formName ?>_<?= $filter->getFilterId() ?>_<?= $id?>" class="prettyCheckbox checkbox list">
                <span class="holderWrap" style="width: 13px; height: 13px; ">
                    <span class="holder" style="width: 13px; "></span>
                </span>
                <?= $option->getName() ?>
            </label>
        </li>
        <? $i++; } ?>

        <? if ($i > 5) { ?>
        <li class="bCtg__eMore" style="padding-left: 10px;">
            <a href="#">еще...</a>
        </li>
        <? } ?>
    </ul>
</dd>