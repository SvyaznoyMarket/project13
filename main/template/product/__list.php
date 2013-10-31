<?php

return function(
    \Helper\TemplateHelper $helper,
    \Iterator\EntityPager $pager,
    array $productVideosByProduct,
    array $bannerPlaceholder = [],
    $view
) { ?>
    <ul class="bListing clearfix">
        <?= $helper->renderWithMustache('product/list/' . ($view == 'line' ? '_line' : '_compact'), (new \View\Product\ListAction())->execute($helper, $pager, $productVideosByProduct, $bannerPlaceholder)) ?>
    </ul>

    <script id="listing_compact_tmpl" type="text/html" data-partial="<?= $helper->json(['cart/_button-product' => file_get_contents(\App::config()->templateDir . '/cart/_button-product.mustache')]) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/product/list/' . ($view == 'line' ? '_line.mustache' : '_compact.mustache')) ?>
    </script>
<? };