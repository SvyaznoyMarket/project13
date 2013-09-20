<?php

return function(
    \Helper\TemplateHelper $helper,
    \Iterator\EntityPager $pager,
    array $productVideosByProduct
) { ?>
    <ul class="bListing clearfix">
        <?= $helper->renderWithMustache('product/list/_compact', (new \View\Product\ListAction())->execute($helper, $pager, $productVideosByProduct)) ?>
    </ul>

    <script id="listing_compact_tmpl" type="text/html" data-partial="<?= $helper->json(['cart/_button-product' => file_get_contents(\App::config()->templateDir . '/cart/_button-product.mustache')]) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/product/list/_compact.mustache') ?>
    </script>
<? };