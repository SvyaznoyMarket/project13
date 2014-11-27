<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    \Model\Product\Filter\Entity $filter
) {
    ?>
    <div>
        от <input name="<?= \View\Name::productCategoryFilter($filter, 'from') ?>" value="<?= $helper->clearZeroValue($productFilter->getValueMin($filter)) ?>" type="text" />
        до <input name="<?= \View\Name::productCategoryFilter($filter, 'to') ?>" value="<?= $helper->clearZeroValue($productFilter->getValueMax($filter)) ?>" type="text" />
        <?= $filter->getUnit() ?>
    </div>
<? };