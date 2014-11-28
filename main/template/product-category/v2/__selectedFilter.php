<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    $baseUrl
) {
    $useBaseUrl = true;
?>

    <div class="js-category-filter-selected">
        <?= $helper->renderWithMustache('product-category/v2/_selectedFilter', (new \View\ProductCategory\SelectedFilterAction())->execute(
            $helper,
            $productFilter,
            $baseUrl,
            $useBaseUrl
        )) ?>
    </div>

    <script id="tplSelectedFilter" type="text/html" data-partial="<?= $helper->json([]) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/product-category/v2/_selectedFilter.mustache') ?>
    </script>

<? };