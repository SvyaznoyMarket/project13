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
        $name = ('shop' === $filter->getId()) ? 'shop' : ('f-' . $filter->getId() . ($filter->getIsMultiple() ? ('-' . \Util\String::slugify($option->getName())) : ''));
    ?>
    <div class="bFilterValuesCol">
        <input
            class="bInputHidden bCustomInput jsCustomRadio"
            type="<?= $filter->getIsMultiple() ? 'checkbox' : 'radio' ?>"
            id="<?= $viewId ?>"
            name="<?= $name ?>"
            value="<?= $optionId ?>"
            hidden
            <? if (in_array($optionId, $values)) { ?> checked="checked"<? } ?>
        />
        <label class="bFilterCheckbox<? if (!$filter->getIsMultiple()) { ?> mCustomLabelRadio<? } ?>" for="<?= $viewId ?>">
            <?= $option->getName() ?>
        </label>
    </div>
    <? $i++; endforeach ?>


<? };