<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter
) {
?>
    <div class="bFilterFoot js-category-filter-selected">
        <?= $helper->renderWithMustache('product-category/_selectedFilter', (new \View\ProductCategory\SelectedFilterAction())->execute(
            $helper,
            $productFilter
        )) ?>
    </div>
    <script id="tplSelectedFilter" type="text/html" data-partial="<?= $helper->json([]) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/product-category/_selectedFilter.mustache') ?>
    </script>

<? };