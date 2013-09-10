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
            class="bInputHidden"
            type="checkbox"
            id="<?= $viewId ?>"
            name=""
            value="<?= $optionId ?>"
            hidden
            <? if (in_array($optionId, $values)) { ?> checked="checked"<? } ?>
        />
        <label class="bFilterCheckbox" for="<?= $viewId ?>">
            <?= $option->getName() ?>
        </label>
    </div>
    <? $i++; endforeach ?>


<? };