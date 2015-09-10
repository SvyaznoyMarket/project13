<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Iterator\EntityPager $pager
 * @param array $bannerPlaceholder
 * @param null $listingStyle
 * @param $view
 * @param null $buyMethod
 * @param bool|true $showState
 * @param int $columnCount
 * @param string $class
 * @param array $cartButtonSender
 * @param \Model\Product\Category\Entity|null $category
 * @param \Model\Favorite\Product\Entity[] $favoriteProductsByUi
 */
$f = function(
    \Helper\TemplateHelper $helper,
    \Iterator\EntityPager $pager,
    array $bannerPlaceholder = [],
    $listingStyle = null,
    $view,
    $buyMethod = null,
    $showState = true,
    $columnCount = 4,
    $class = '',
    array $cartButtonSender = [],
    \Model\Product\Category\Entity $category = null,
    $favoriteProductsByUi = []
) {

    $listingClass = '';

    $partials = [
        'cart/_button-product'      => file_get_contents(\App::config()->templateDir . '/cart/_button-product.mustache'),
        'cart/_button-product-lstn' => file_get_contents(\App::config()->templateDir . '/cart/_button-product-lstn.mustache'),
        'product/_review-compact'   => file_get_contents(\App::config()->templateDir . '/product/_review-compact.mustache')
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

    $chosenTestCase = \App::abTest()->getTest('siteListingWithViewSwitcher')->getChosenCase()->getKey();
    $chosenCategoryView = \App::request()->cookies->get('categoryView');
    if (((($chosenTestCase === 'compactWithSwitcher' && $chosenCategoryView === 'expanded') || ($chosenTestCase === 'expandedWithSwitcher' && $chosenCategoryView !== 'compact')) || $chosenTestCase === 'expandedWithoutSwitcher') && $category && $category->isInSiteListingWithViewSwitcherAbTest()) {
        $defaultTemplatePath = $expandedTemplatePath;
        $defaultView = 'expanded';
        $listingClass = 'listing';
    } else {
        $defaultTemplatePath = $compactTemplatePath;
        $defaultView = $view;
    }
?>

    <ul class="bListing <? if (3 === $columnCount): ?> bListing-3col<? endif ?> clearfix<? if ('jewel' === $listingStyle): ?> mPandora<? endif ?> <?= $listingClass ?> <?= $class ?> js-listing" <? if ($defaultView === 'expanded'): ?>data-category-view="<?= $defaultView ?>"<? endif ?>><!-- mPandora если необходимо застилить листинги под пандору -->
        <?= $helper->renderWithMustache($defaultTemplatePath, (new \View\Product\ListAction())->execute($helper, $pager, $bannerPlaceholder, $buyMethod, $showState, $columnCount, $defaultView, $cartButtonSender, $category, $favoriteProductsByUi)) ?>
    </ul>

    <script id="listing_compact_tmpl" type="text/html" data-partial="<?= $helper->json($partials) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/' . $compactTemplatePath . '.mustache') ?>
    </script>

    <? if ($chosenTestCase === 'compactWithSwitcher' || $chosenTestCase === 'expandedWithSwitcher' || $chosenTestCase === 'expandedWithoutSwitcher'): ?>
        <script id="listing_expanded_tmpl" type="text/html" data-partial="<?= $helper->json($partials) ?>">
            <?= file_get_contents(\App::config()->templateDir . '/' . $expandedTemplatePath . '.mustache') ?>
        </script>
    <? endif ?>
<? }; return $f;