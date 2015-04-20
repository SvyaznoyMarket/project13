<?php

return function(
    \Helper\TemplateHelper $helper,
    \Iterator\EntityPager $pager,
    array $bannerPlaceholder = [],
    $listingStyle = null,
    $view,
    $buyMethod = null,
    $showState = true,
    $columnCount = 4,
    $class = '',
    array $cartButtonSender = []
) {
    $partials = [
        'cart/_button-product' => file_get_contents(\App::config()->templateDir . '/cart/_button-product.mustache'),
        'product/_review-compact' => file_get_contents(\App::config()->templateDir . '/product/_review-compact.mustache')
    ]; ?>

    <?php switch ($view) {
        case 'light_with_bottom_description':
            $listingClass = 'lstn';
            $templatePath = 'product/list/_light';
            break;
        case 'light_with_hover_bottom_description':
            $listingClass = 'lstn';
            $templatePath = 'product/list/_light';
            break;
        case 'light_without_description':
            $listingClass = 'lstn-light lstn';
            $templatePath = 'product/list/_light';
            break;
        default:
            $listingClass = '';
            $templatePath = 'product/list/_compact';
            break;
    } ?>

    <ul class="bListing<? if (3 === $columnCount): ?> bListing-3col<? endif ?> clearfix<? if ('jewel' === $listingStyle): ?> mPandora<? endif ?> <?= $listingClass ?> <?= $class ?> js-listing"><!-- mPandora если необходимо застилить листинги под пандору -->
        <?= $helper->renderWithMustache($templatePath, (new \View\Product\ListAction())->execute($helper, $pager, $bannerPlaceholder, $buyMethod, $showState, $columnCount, $view, $cartButtonSender)) ?>
    </ul>

    <script id="listing_compact_tmpl" type="text/html" data-partial="<?= $helper->json($partials) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/' . $templatePath . '.mustache') ?>
    </script>
<? };