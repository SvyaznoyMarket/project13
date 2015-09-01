<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    $baseUrl
) {
    ?>

    <div class="fltrBtn_kit fltrBtn_kit-nborder">
        <div class="js-category-filter-selected clearfix">
            <?= $helper->renderWithMustache('category/filters/selected.filters', (new \View\ProductCategory\SelectedFilterAction())->execute(
                $helper,
                $productFilter,
                $baseUrl
            )) ?>
        </div>
    </div>



<? };