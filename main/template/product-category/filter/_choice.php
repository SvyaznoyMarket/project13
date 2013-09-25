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

<?php $values = $productFilter->getValue($filter) ?>

<dt<? if (5 > $index) { ?> class="<?= ((1 == $index) ? ' first' : '') ?>"<? } ?>>
    <?= $filter->getName() ?>
</dt>

<dd style="display: <?= $isOpened ? 'block' : 'none' ?>;">
    <ul class="checkbox_list">
        <? foreach (array('нет', 'да') as $id => $name) { $id = (int)$id ?>
        <li>
            <input name="<?= $formName ?>[<?= $filter->getId()?>][]" type="checkbox" value="<?= $id?>" <? if (in_array($id, $values)) echo 'checked' ?> id="<?= $formName ?>_<?= $filter->getId()?>_<?= $id?>" class="hiddenCheckbox"/>
            <label for="<?= $formName ?>_<?= $filter->getId()?>_<?= $id?>" class="prettyCheckbox checkbox list">
                <?= $name?>
            </label>
        </li>
        <? } ?>
    </ul>
</dd>