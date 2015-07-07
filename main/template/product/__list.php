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

    $listingClass = '';

    $partials = [
        'cart/_button-product' => file_get_contents(\App::config()->templateDir . '/cart/_button-product.mustache'),
        'product/_review-compact' => file_get_contents(\App::config()->templateDir . '/product/_review-compact.mustache')
    ];

    switch ($view) {
        case 'light_with_bottom_description':
            $listingClass = 'lstn';
            $compactTemplatePath = 'product/list/_light';
            break;
        case 'light_with_hover_bottom_description':
            $listingClass = 'lstn';
            $compactTemplatePath = 'product/list/_light';
            break;
        case 'light_without_description':
            $listingClass = 'lstn-light lstn';
            $compactTemplatePath = 'product/list/_light';
            break;
        default:
            $compactTemplatePath = 'product/list/_compact';
            break;
    }

    $expandedTemplatePath = 'product/list/_expanded';

    $chosenCase = \App::abTest()->getTest('siteListingWithViewSwitcher')->getChosenCase()->getKey();
    if ((($chosenCase === 'compactWithSwitcher' || $chosenCase === 'expandedWithSwitcher') && \App::request()->cookies->get('categoryView') === 'expanded') || $chosenCase === 'expandedWithoutSwitcher') {
        $defaultTemplatePath = $expandedTemplatePath;
        $defaultView = 'expanded';
        $listingClass = 'listing';
    } else {
        $defaultTemplatePath = $compactTemplatePath;
        $defaultView = $view;
    }
?>

    <ul class="bListing <? if (3 === $columnCount): ?> bListing-3col<? endif ?> clearfix<? if ('jewel' === $listingStyle): ?> mPandora<? endif ?> <?= $listingClass ?> <?= $class ?> js-listing"><!-- mPandora если необходимо застилить листинги под пандору -->
        <?= $helper->renderWithMustache($defaultTemplatePath, (new \View\Product\ListAction())->execute($helper, $pager, $bannerPlaceholder, $buyMethod, $showState, $columnCount, $defaultView, $cartButtonSender)) ?>
    </ul>

    <script id="listing_compact_tmpl" type="text/html" data-partial="<?= $helper->json($partials) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/' . $compactTemplatePath . '.mustache') ?>
    </script>

    <? if ($chosenCase === 'compactWithSwitcher' || $chosenCase === 'expandedWithSwitcher' || $chosenCase === 'expandedWithoutSwitcher'): ?>
        <script id="listing_expanded_tmpl" type="text/html" data-partial="<?= $helper->json($partials) ?>">
            <?= file_get_contents(\App::config()->templateDir . '/' . $expandedTemplatePath . '.mustache') ?>
        </script>
    <? endif ?>
<? };