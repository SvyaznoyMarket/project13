<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    \Model\Product\Filter\Entity $filter
) {
    ?>
    <div class="js-productCategory-filter2-element-number">
        от <input name="<?= \View\Name::productCategoryFilter($filter, 'from') ?>" placeholder="<?= $helper->clearZeroValue($productFilter->getValueMin($filter)) ?>" type="text" />
        до <input name="<?= \View\Name::productCategoryFilter($filter, 'to') ?>" placeholder="<?= $helper->clearZeroValue($productFilter->getValueMax($filter)) ?>" type="text" />
        <?= $filter->getUnit() ?>
    </div>
<? };