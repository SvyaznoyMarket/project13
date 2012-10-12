<?php

use Model\Product\Filter\Entity as FilterEntity;

/**
 * @var $page          \View\DefaultLayout
 * @var $category      \Model\Product\Category\Entity
 * @var $productFilter \Model\Product\Filter
 * @var $filter        \Model\Product\Filter\Entity
 * @var $isOpened      bool
 * @var $index         int
 */
?>

<?php $values = $productFilter->getValue($filter) ?>

<dt<? if (5 > $index) { ?> class="<?= ((1 == $index) ? ' first' : '') ?>"<? } ?>>
    <?= $filter->getName() ?>
</dt>

<dd style="display: <?= $isOpened ? 'block' : 'none' ?>;">
    <ul class="checkbox_list">
        <?  ?>
        <? foreach (array('нет', 'да') as $id => $name) { $id = (int)$id ?>
        <li>
            <label for="<?= $name ?>_<?= $filter->getFilterId()?>_<?= $id?>" class="prettyCheckbox checkbox list">
                <input name="<?= $name ?>[<?= $filter->getFilterId()?>][]" type="checkbox" value="<?= $id?>" <? if (in_array($id, $values)) echo 'checked' ?> id="<?= $name ?>_<?= $filter->getFilterId()?>_<?= $id?>" class="hiddenCheckbox"/>
                <span class="holderWrap" style="width: 13px; height: 13px;">
                    <span class="holder" style="width: 13px; "></span>
                </span>
                <?= $name?>
            </label>
        </li>
        <? } ?>
    </ul>
</dd>