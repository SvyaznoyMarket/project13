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

    <?php switch ($view) {
        case 'light_with_bottom_description':
            $listingClass = 'lstn';
            $templatePath = 'product/list/_light';
            $templateView = [
                'extraContent' => true,
                'bottomDescription' => true,
                'hoverDescription' => false,
            ];
            break;
        case 'light_with_hover_bottom_description':
            $listingClass = 'lstn';
            $templatePath = 'product/list/_light';
            $templateView = [
                'extraContent' => true,
                'bottomDescription' => true,
                'hoverDescription' => true,
            ];
            break;
        case 'light_without_description':
            $listingClass = 'lstn lstn-light';
            $templatePath = 'product/list/_light';
            $templateView = [
                'extraContent' => false,
                'bottomDescription' => false,
                'hoverDescription' => false,
            ];
            break;
        case 'line':
            $listingClass = '';
            $templatePath = 'product/list/_line';
            $templateView = [];
            break;
        default:
            $listingClass = '';
            $templatePath = 'product/list/_compact';
            $templateView = [];
            break;
    } ?>

    <ul class="bListing<? if (3 === $columnCount): ?> bListing-3col<? endif ?> clearfix<? if ('jewel' === $listingStyle): ?> mPandora<? endif ?> <?= $listingClass ?> js-listing"><!-- mPandora если необходимо застилить листинги под пандору -->
        <?= $helper->renderWithMustache($templatePath, (new \View\Product\ListAction())->execute($helper, $pager, $productVideosByProduct, $bannerPlaceholder, $buyMethod, $showState, $columnCount, $templateView)) ?>
    </ul>

    <script id="listing_compact_tmpl" type="text/html" data-partial="<?= $helper->json($partials) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/' . $templatePath . '.mustache') ?>
    </script>
<? };