<?php

return function(
    \Helper\TemplateHelper $helper,
    \Iterator\EntityPager $pager,
    array $productVideosByProduct,
    array $bannerPlaceholder = [],
    $listingStyle = null,
    $view,
    $buyMethod = null,
    $showState = true,
    $columnCount = 4
) {
    $partials = [
        'cart/_button-product' => file_get_contents(\App::config()->templateDir . '/cart/_button-product.mustache'),
        'product/_review-compact' => file_get_contents(\App::config()->templateDir . '/product/_review-compact.mustache')
    ]; ?>

    <ul class="bListing<? if (3 === $columnCount): ?> bListing-3col<? endif ?> clearfix<? if ('jewel' === $listingStyle): ?> mPandora<? endif ?> js-listing"><!-- mPandora если необходимо застилить листинги под пандору -->
        <?= $helper->renderWithMustache('product/list/' . ($view == 'line' ? '_line' : '_compact'), (new \View\Product\ListAction())->execute($helper, $pager, $productVideosByProduct, $bannerPlaceholder, $buyMethod, $showState, $columnCount)) ?>
    </ul>

    <script id="listing_compact_tmpl" type="text/html" data-partial="<?= $helper->json($partials) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/product/list/' . ($view == 'line' ? '_line.mustache' : '_compact.mustache')) ?>
    </script>
<? };