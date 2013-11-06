<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    \Model\Product\Filter\Entity $filter
) {
    $values = $productFilter->getValue($filter);
?>


    <? $i = 0; foreach ($filter->getOption() as $option): ?>
    <?
        $optionId = $option->getId();
        $viewId = \View\Id::productCategoryFilter($filter->getId()) . '-option-' . $optionId;
    ?>
    <div class="bFilterValuesCol">
        <input
            class="bInputHidden bCustomInput jsCustomRadio"
            type="<?= $filter->getIsMultiple() ? 'checkbox' : 'radio' ?>"
            id="<?= $viewId ?>"
            name="<?= \View\Name::productCategoryFilter($filter, $option) ?>"
            value="<?= $optionId ?>"
            hidden
            <? if (in_array($optionId, $values)) { ?> checked="checked"<? } ?>
        />
        <label class="bFilterCheckbox<? if (!$filter->getIsMultiple()) { ?> mCustomLabelRadio<? } ?>" for="<?= $viewId ?>">
            <?= $option->getName() ?><?= $option->getQuantity() ? " ({$option->getQuantity()})" : '' ?>
        </label>
    </div>
    <? $i++; endforeach ?>


<? };