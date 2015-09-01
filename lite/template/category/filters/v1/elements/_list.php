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

    <? foreach ($filter->getOption() as $option): ?>
        <?
        $optionId = $option->getId();
        $viewId = \View\Id::productCategoryFilter($filter->getId()) . '-option-' . $optionId;
        ?>

        <div class="filter-values__cell">
            <input
                class="custom-input <?= $filter->getIsMultiple() ? 'filter-check' : 'filter-radio' ?> jsCustomRadio js-customInput"
                type="<?= $filter->getIsMultiple() ? 'checkbox' : 'radio' ?>"
                id="<?= $viewId ?>"
                name="<?= \View\Name::productCategoryFilter($filter, $option) ?>"
                value="<?= $optionId ?>"
                <? if (in_array($optionId, $values) || $optionId === $categoryId) { ?> checked="checked"<? } ?>
                />
            <label class="custom-label" for="<?= $viewId ?>">
                <?= $option->getName() ?><?= ($showFasets && $option->getQuantity()) ? " ({$option->getQuantity()})" : '' ?>
            </label>
        </div>
    <? endforeach ?>
<? };