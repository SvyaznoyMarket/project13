<?php
/**
 * @var $page                   \View\ProductCategory\LeafPage
 * @var $category               \Model\Product\Category\Entity
 * @var $brand                  \Model\Brand\Entity|null
 * @var $productFilter          \Model\Product\Filter
 * @var $productPager           \Iterator\EntityPager
 * @var $productSorting         \Model\Product\Sorting
 * @var $productView            string
 * @var $hotlinks               array
 * @var $seoContent             string
 * @var $relatedCategories      array
 * @var $categoryConfigById     array
 * @var $slideData              array
 */
?>

<?
$helper = new \Helper\TemplateHelper();
if ($productFilter->getShop()) $page->setGlobalParam('shop', $productFilter->getShop());

// получаем стиль листинга
$listingStyle = !empty($catalogJson['listing_style']) ? $catalogJson['listing_style'] : null;

// получаем promo стили
$promoStyle = 'jewel' === $listingStyle && isset($catalogJson['promo_style']) ? $catalogJson['promo_style'] : [];
$category_class = !empty($catalogJson['category_class']) ? strtolower(trim((string)$catalogJson['category_class'])) : null;
?>

<?= $helper->render('product-category/__data', ['category' => $category]) ?>

<div class="bCatalog <? if ($category->isV3()): ?>bCatalog-custom<? endif ?> <?= 'jewel' === $listingStyle ? 'mCustomCss' : '' ?>" id="bCatalog" data-lastpage="<?= $productPager->getLastPage() ?>">

    <?= $helper->render('product-category/__breadcrumbs', ['category' => $category, 'isBrand' => isset($brand)]) // хлебные крошки ?>

    <div class="bCustomFilter"<? if(!empty($promoStyle['promo_image'])): ?> style="<?= $promoStyle['promo_image'] ?>"<? endif ?>>
        <h1 class="bTitlePage js-pageTitle"<? if(!empty($promoStyle['title'])): ?> style="<?= $promoStyle['title'] ?>"<? endif ?>><?= $title ?></h1>

        <? if (\App::config()->adFox['enabled']): ?>
            <!-- Баннер --><div id="adfox683sub" class="adfoxWrapper bBannerBox"></div><!--/ Баннер -->
        <? endif ?>

        <? if((bool)$slideData): ?>
            <?= $helper->render('tchibo/promo-catalog', ['slideData' => $slideData, 'categoryToken' => $category->getRoot() ? $category->getRoot()->getToken() : '']) // promo slider ?>
        <? endif ?>

        <? if (!empty($promoContent)): ?>
            <?= $promoContent ?>
        <? else : ?>
            <?= $helper->render('product-category/__children',
                [
                    'category'           => $category,
                    'promoStyle'         => $promoStyle,
                    'relatedCategories'  => $relatedCategories,
                    'categoryConfigById' => $categoryConfigById,
                    'productPager'       => $productPager,
                    'category_class'     => $category_class,
                    'showFullChildren'   => $category->isShowFullChildren(),
                ]
            ) // дочерние категории and relatedCategories ?>
        <? endif ?>

        <? if ($category->isShowSmartChoice()): ?>
            <?= $helper->render('product/__smartChoice', ['smartChoiceProducts' => $smartChoiceProducts]); ?>
        <? endif ?>

        <? if ($category->isV2()): ?>
            <?= $helper->render('product-category/v2/__filter', [
                'baseUrl'       => $helper->url('product.category', ['categoryPath' => $category->getPath()]),
                'productFilter' => $productFilter,
            ]) // фильтры ?>
        <? elseif ($category->isV3()): ?>
            <?= $helper->render('product-category/v3/__filter', [
                'baseUrl'       => $helper->url('product.category', ['categoryPath' => $category->getPath()]),
                'productFilter' => $productFilter,
                'openFilter'    => false,
                'promoStyle'    => $promoStyle,
            ]) // фильтры ?>
        <? else: ?>
            <?= $helper->render('product-category/__filter', [
                'baseUrl'       => $helper->url('product.category', ['categoryPath' => $category->getPath()]),
                'productFilter' => $productFilter,
                'openFilter'    => false,
                'promoStyle'    => $promoStyle,
                'hasBanner'     => isset($hasBanner) ? (bool)$hasBanner : false,
                'productPager'  => $productPager,
            ]) // фильтры ?>
        <? endif ?>


        <? if ($category->isV2() || (in_array(\App::abTest()->getTest('siteListingWithViewSwitcher')->getChosenCase()->getKey(), ['compactWithSwitcher', 'expandedWithSwitcher', 'expandedWithoutSwitcher'], true) && $category && $category->isInSiteListingWithViewSwitcherAbTest())): ?>
            <?= $helper->render('product-category/v2/__listAction', [
                'pager'          => $productPager,
                'productSorting' => $productSorting,
                'category'       => $category,
            ]) // сортировка, режим просмотра, режим листания ?>
        <? else: ?>
            <?= $helper->render('product/__listAction', [
                'pager'          => $productPager,
                'productSorting' => $productSorting,
                'category'       => $category,
            ]) // сортировка, режим просмотра, режим листания ?>
        <? endif ?>
    </div>

    <?= $helper->render('product/__list', [
        'pager'                  => $productPager,
        'view'                   => $productView,
        'bannerPlaceholder'      => !empty($catalogJson['bannerPlaceholder']) && 'jewel' !== $listingStyle ? $catalogJson['bannerPlaceholder'] : [],
        'listingStyle'           => $listingStyle,
        'columnCount'            => isset($columnCount) ? $columnCount : 4,
        'class'                  => $category->isV2Furniture() && \Session\AbTest\AbTest::isNewFurnitureListing() ? 'lstn-btn2' : '',
        'category'               => $category,
    ]) // листинг ?>

    <? if ($category->isV2()): ?>
        <div class="sorting clearfix js-category-sortingAndPagination">
            <?= $helper->render('product-category/v2/__pagination', ['pager' => $productPager, 'category' => $category]) // листалка ?>
        </div>
    <? else: ?>
        <div class="bSortingLine mPagerBottom clearfix js-category-sortingAndPagination">
            <?= $helper->render('product/__pagination', ['pager' => $productPager, 'category' => $category]) // листалка ?>
        </div>
    <? endif ?>

    <? if (\App::config()->product['pullRecommendation'] && \App::config()->product['viewedEnabled']): ?>
        <?= $helper->render('product/__slider', [
            'type'      => 'viewed',
            'title'     => 'Вы смотрели',
            'products'  => [],
            'limit'     => \App::config()->product['itemsInSlider'],
            'page'      => 1,
            'url'       => $page->url('product.recommended'),
            'sender'    => [
                'name'     => 'enter',
                'position' => 'Viewed',
                'from'     => 'categoryPage'
            ],
        ]) ?>
    <? endif ?>

    <? if (!empty($seoContent) || (bool)$hotlinks): ?>
        <div class="bSeoText">
            <?= $seoContent ?>

            <?= $helper->render('product-category/__hotlink', ['hotlinks' => $hotlinks, 'promoStyle' => $promoStyle]) // hotlinks ?>
        </div>
    <? endif ?>

</div>