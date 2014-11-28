<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    \Model\Product\Filter\Entity $filter
) {
    ?>
    <div class="fltrBtn_ln js-category-v2-filter-element-number">
        <span class="fltrBtn_param_lbl txmark1">от</span> <input class="fltrBtn_param_it" name="<?= \View\Name::productCategoryFilter($filter, 'from') ?>" placeholder="<?= $helper->clearZeroValue($productFilter->getValueMin($filter)) ?>" type="text" />
        &ensp;<span class="fltrBtn_param_lbl txmark1">до</span> <input class="fltrBtn_param_it" name="<?= \View\Name::productCategoryFilter($filter, 'to') ?>" placeholder="<?= $helper->clearZeroValue($productFilter->getValueMax($filter)) ?>" type="text" />
        <span class="fltrBtn_param_lbl txmark1"><?= $filter->getUnit() ?></span>
    </div>
<? };