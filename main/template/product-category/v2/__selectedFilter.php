<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter
) {
?>

    <div class="js-category-filter-selected clearfix">
        <?= $helper->renderWithMustache('product-category/v2/_selectedFilter', (new \View\Partial\ProductCategory\V2\SelectedFilter())->execute(
            $helper,
            $productFilter
        )) ?>
    </div>

    <script id="tplSelectedFilter" type="text/html" data-partial="<?= $helper->json([]) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/product-category/v2/_selectedFilter.mustache') ?>
    </script>

<? };