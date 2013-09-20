<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    $baseUrl
) {
    $data = (new \View\ProductCategory\SelectedFilterAction())->execute(
        $helper,
        $productFilter,
        $baseUrl
    );

    if (empty($data['filters'])) {
        return;
    }
?>
    <div class="bFilterFoot">
        <?= $helper->renderWithMustache('product-category/_selectedFilter', $data) ?>
    </div>
    <script id="tplSelectedFilter" type="text/html" data-partial="<?= $helper->json([]) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/product-category/_selectedFilter.mustache') ?>
    </script>

<? };