<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    $baseUrl
) {
    ?>

    <div class="js-category-filter-selected clearfix">
        <?= $helper->renderWithMustache('category/filters/selected.filters', (new \View\Partial\ProductCategory\V2\SelectedFilter())->execute(
            $helper,
            $productFilter,
            $baseUrl
        )) ?>
    </div>



<? };