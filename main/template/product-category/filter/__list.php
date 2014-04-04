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
    $disabledFilters = $helper->getParam('disabledFilters');
?>


    <? $i = 0; foreach ($filter->getOption() as $option): ?>
    <?
        $optionId = $option->getId();
        $viewId = \View\Id::productCategoryFilter($filter->getId()) . '-option-' . $optionId;
        $name = \View\Name::productCategoryFilter($filter, $option);

        $hideOption = false;
        if ($disabledFilters && \App::config()->sphinx['showFacets']) {
            if ('shop' === $filter->getId()) {
                if (isset($disabledFilters[$filter->getId()]) && in_array($optionId, $disabledFilters[$filter->getId()])) {
                    $hideOption =  true;
                }
            } elseif (in_array($name, array_keys($disabledFilters))) {
                $hideOption = true;
            }
        }
    ?>
    <div class="bFilterValuesCol<?= $filter->isBrand() ? ' bFilterBand' : '' ?>"<? if ($hideOption): ?> style="display:none;"<? endif ?>>
        <input
            class="bInputHidden bCustomInput jsCustomRadio"
            type="<?= $filter->getIsMultiple() ? 'checkbox' : 'radio' ?>"
            id="<?= $viewId ?>"
            name="<?= $name ?>"
            value="<?= $optionId ?>"
            <? if ($filter->isBrand()) { echo 'data-name="',$option->getName(),'"'; } ?>
            <? if (in_array($optionId, $values) || $optionId === $categoryId) { ?> checked="checked"<? } ?>
        />
        <label class="bFilterCheckbox<? if (!$filter->getIsMultiple()) { ?> mCustomLabelRadio<? } ?>" for="<?= $viewId ?>">
            <?= $option->getName() ?><?= ($showFasets && $option->getQuantity()) ? " <span class='facet'>({$option->getQuantity()})</span>" : '' ?>
        </label>
    </div>
    <? $i++; endforeach ?>


<? };