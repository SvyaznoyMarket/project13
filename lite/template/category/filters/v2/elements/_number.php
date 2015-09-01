<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    \Model\Product\Filter\Entity $filter
) {
    $minValue = $helper->clearZeroValue($productFilter->getValueMin($filter));
    $maxValue = $helper->clearZeroValue($productFilter->getValueMax($filter));
    $placeholderMinValue = $helper->clearZeroValue($filter->getMin());
    $placeholderMaxValue = $helper->clearZeroValue($filter->getMax());

    if ($minValue === $placeholderMinValue) {
        $minValue = '';
    }

    if ($maxValue === $placeholderMaxValue) {
        $maxValue = '';
    }
    ?>
    <div class="fltrBtn_ln">
        <span class="fltrBtn_param_lbl txmark1">от</span> <input class="fltrBtn_param_it" name="<?= \View\Name::productCategoryFilter($filter, 'from') ?>" value="<?= $minValue ?>" placeholder="<?= $placeholderMinValue ?>" type="text" />
        &ensp;<span class="fltrBtn_param_lbl txmark1">до</span> <input class="fltrBtn_param_it" name="<?= \View\Name::productCategoryFilter($filter, 'to') ?>" value="<?= $maxValue ?>" placeholder="<?= $placeholderMaxValue ?>" type="text" />
        <span class="fltrBtn_param_lbl txmark1"><?= $filter->getUnit() ?></span>
    </div>
<? };