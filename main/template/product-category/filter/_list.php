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
        <? $i = 0; foreach ($filter->getOption() as $option) { $id = $option->getId() ?>
        <li<? if ($i > 4) { ?> class="hf" style="display: none"<? } ?>>
            <input name="<?= $formName ?>[<?= $filter->getId()?>][]" type="checkbox" value="<?= $id ?>"<? if (in_array($id, $values)) { ?> checked="checked"<? } ?> id="<?= $formName ?>_<?= $filter->getId() ?>_<?= $id ?>" class="hiddenCheckbox" />
            <label for="<?= $formName ?>_<?= $filter->getId() ?>_<?= $id?>" class="prettyCheckbox checkbox list">
                <? $quantity = empty(\App::config()->sphinx['showFacets']) ? 0 : $option->getQuantity() ?>
                <?= $option->getName() ?> <? if(!empty($quantity)): ?><span class="gray">(<?= $quantity ?>)</span><? endif ?>
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