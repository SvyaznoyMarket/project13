<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    \Model\Product\Filter\Entity $filter
) {
    $values = $productFilter->getValue($filter);
    $category = $helper->getParam('selectedCategory');
    $categoryId = $category ? $category->getId() : null;

    $showFasets = \App::config()->sphinx['showFacets'];
?>


    <? $i = 0; foreach ($filter->getOption() as $option): ?>
    <?
        $optionId = $option->getId();
        $viewId = \View\Id::productCategoryFilter($filter->getId()) . '-option-' . $optionId;
    ?>
    <div class="bFilterValuesCol<?= $filter->isBrand() ? ' bFilterBand' : '' ?>">
        <input
            class="bInputHidden bCustomInput jsCustomRadio"
            type="<?= $filter->getIsMultiple() ? 'checkbox' : 'radio' ?>"
            id="<?= $viewId ?>"
            name="<?= \View\Name::productCategoryFilter($filter, $option) ?>"
            value="<?= $optionId ?>"
            <? if ($filter->isBrand()) { echo 'data-name="',$option->getName(),'"'; } ?>
            <? if (in_array($optionId, $values) || $optionId === $categoryId) { ?> checked="checked"<? } ?>
        />
        <label class="bFilterCheckbox<? if (!$filter->getIsMultiple()) { ?> mCustomLabelRadio<? } ?>" for="<?= $viewId ?>">
            <?= $option->getName() ?><?= ($showFasets && $option->getQuantity()) ? " ({$option->getQuantity()})" : '' ?>
        </label>
    </div>
    <? $i++; endforeach ?>


<? };