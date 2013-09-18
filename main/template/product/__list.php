<?php

return function(
    \Helper\TemplateHelper $helper,
    \Iterator\EntityPager $pager,
    array $productVideosByProduct
) {
    $data = (new \View\Product\ListAction())->execute(
        $helper,
        $pager,
        $productVideosByProduct
    );

    $partials = [
        'cart/_button-product' => file_get_contents(\App::config()->templateDir . '/cart/_button-product.mustache'),
    ];
?>
    <ul class="bListing clearfix">
        <?= $helper->renderWithMustache('product/list/_compact', $data) ?>
    </ul>

    <script id="listing_compact_tmpl" type="text/html" data-partial="<?= $helper->json($partials) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/product/list/_compact.mustache') ?>
    </script>
<? };