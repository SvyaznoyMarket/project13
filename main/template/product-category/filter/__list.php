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
    $disabledFilters = isset($disabledFilters['list']) ? $disabledFilters['list'] : null;

    $changedFilters = $helper->getParam('changedFilters');
    $changedFilters = isset($changedFilters['list']) ? $changedFilters['list'] : null;
?>


    <? $i = 0; foreach ($filter->getOption() as $option): ?>
    <?
        $optionId = $option->getId();
        $viewId = \View\Id::productCategoryFilter($filter->getId()) . '-option-' . $optionId;
        $name = \View\Name::productCategoryFilter($filter, $option);

        $hideOption = false;
        $quantity = $option->getQuantity() ? $option->getQuantity() : null;
        if ($showFasets) {
            if ($disabledFilters) {
                if ('shop' === $filter->getId()) {
                    if (isset($disabledFilters[$filter->getId()]) && in_array($optionId, $disabledFilters[$filter->getId()])) {
                        $hideOption =  true;
                    }
                } elseif (in_array($name, array_keys($disabledFilters))) {
                    $hideOption = true;
                }
            }

            // обновление значений
            if ((bool)$changedFilters) {
                if (in_array($name, array_keys($changedFilters))) {
                    if ('shop' === $name) {
                        if (in_array($optionId, array_keys($changedFilters[$name]))) {
                            $quantity = $changedFilters[$name][$optionId];
                        }
                    } else {
                        $quantity = $changedFilters[$name];
                    }
                }
            }
        }
    ?>
    <div class="bFilterValuesCol jsFilterList<?= $filter->isBrand() ? ' bFilterBand' : '' ?>"<? if ($hideOption): ?> style="display:none;"<? endif ?>>
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
            <?= $option->getName() ?>
            <? if ($showFasets && $quantity): ?> <span class='facet'>(<?= $quantity ?>)</span><? endif ?>
        </label>
    </div>
    <? $i++; endforeach ?>


<? };