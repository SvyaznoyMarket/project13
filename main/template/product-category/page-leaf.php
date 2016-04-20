<?php
/**
 * @var $page                   \View\ProductCategory\LeafPage
 * @var $category               \Model\Product\Category\Entity
 * @var $brand                  \Model\Brand\Entity|null
 * @var $productFilter          \Model\Product\Filter
 * @var $productPager           \Iterator\EntityPager
 * @var $productSorting         \Model\Product\Sorting
 * @var $hotlinks               array
 * @var $seoContent             string
 * @var $relatedCategories      array
 * @var $categoryConfigById     array
 * @var $slideData              array
 * @var $menu                   \Model\Menu\BasicMenuEntity[]
 * @var array $listViewData
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

// background image для title в Чибе
$titleBackgroundStyle = '';
if ($category->isTchibo()) {
    $titleBackgroundStyle = $category->getMediaSource('category_wide_940x150', 'category_wide')->url
        ? sprintf("background-image: url('%s')", $category->getMediaSource('category_wide_940x150', 'category_wide')->url)
        : '';
}
$siblingCategories = $rootCategoryInMenu ? $rootCategoryInMenu->getChild() : [];

$menuChar = null;
if (isset($menu) && is_array($menu)) {
    foreach ($menu as $menuEntity) {
        if ($category->getName() === $menuEntity->name) {
            $menuChar = $menuEntity->char;
            break;
        }
    }
}
?>

<?= $helper->render('product-category/__data', ['category' => $category]) ?>

<div class="bCatalog <? if ($category->isV3()): ?>bCatalog-custom<? endif ?> <?= 'jewel' === $listingStyle ? 'mCustomCss' : '' ?>" id="bCatalog" data-lastpage="<?= $productPager->getLastPage() ?>">

    <? if ($category->isTchibo()) : ?>
        <?= $helper->render('product-category/__sibling-list', ['categories' => $siblingCategories, 'currentCategory'    => $category,
            'rootCategoryInMenu' => $rootCategoryInMenu]) ?>
    <? else : ?>
        <?= $helper->render('product-category/__breadcrumbs', ['category' => $category, 'brand' => $brand]) // хлебные крошки ?>
    <? endif ?>

    <div class="bCustomFilter"<? if(!empty($promoStyle['promo_image'])): ?> style="<?= $promoStyle['promo_image'] ?>"<? endif ?>>

        <? if ($titleBackgroundStyle) : ?>
            <h1 class="bTitlePage_tchibo js-pageTitle" style="<?= $titleBackgroundStyle ?>"><?= $title ?></h1>
        <? else : ?>
            <h1 class="bTitlePage js-pageTitle"<? if(!empty($promoStyle['title'])): ?> style="<?= $promoStyle['title'] ?>"<? endif ?>><?= $title ?>
            <? if ($category->isGridWithListing()) : ?>
                <div class="bCatalog__all-product">
                    <a href="#productCatalog-filter-form" class="jsCategoryGridShowGoodsLink">
                        <? if ($menuChar) : ?><span class="bCatalog__all-product-icon"><?= $menuChar ?></span><? endif ?>
                        <span class="bCatalog__all-product-txt">Смотреть все товары</span>
                    </a>
                </div>
            <? endif ?>
            </h1>
        <? endif ?>

        <? if (\App::config()->adFox['enabled']): ?>
            <? if ($category->isGrid() || $category->isGridWithListing()): ?>
                <!-- Баннер --><div id="adfox683" class="adfoxWrapper bBannerBox"></div><!--/ Баннер -->
            <? else: ?>
                <!-- Баннер --><div id="adfox683sub" class="adfoxWrapper bBannerBox"></div><!--/ Баннер -->
            <? endif ?>
        <? endif ?>

        <? if((bool)$slideData && !$category->isTchibo()): ?>
            <?= $helper->render('tchibo/promo-catalog', ['slideData' => $slideData, 'categoryToken' => $category->getRoot() ? $category->getRoot()->getToken() : '']) // promo slider ?>
        <? endif ?>

        <? if (!empty($promoContent) && !$category->isGrid() && !$category->isGridWithListing()): ?>
            <?= $promoContent ?>
        <? elseif ($category->isGrid() || $category->isGridWithListing()) : ?>
            <?= $page->render('product-category/grid/grid-category', ['categories' => $category->getChild()]) ?>
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

        <? if (!$category->isGrid()) : ?>

        <? if ($category->isShowSmartChoice()): ?>
            <?= $helper->render('product/__smartChoice', ['smartChoiceProducts' => $smartChoiceProducts]); ?>
        <? endif ?>

        <? if ($category->isV2()): ?>
            <?= $helper->render('product-category/v2/__filter', [
                'baseUrl'       => $category->getLink(),
                'productFilter' => $productFilter,
                'category'      => $category,
            ]) // фильтры ?>
        <? elseif ($category->isV3()): ?>
            <?= $helper->render('product-category/v3/__filter', [
                'baseUrl'       => $category->getLink(),
                'productFilter' => $productFilter,
                'openFilter'    => false,
                'promoStyle'    => $promoStyle,
            ]) // фильтры ?>
        <? else: ?>
            <?= $helper->render('product-category/__filter', [
                'baseUrl'       => $category->getLink(),
                'productFilter' => $productFilter,
                'openFilter'    => false,
                'promoStyle'    => $promoStyle,
                'hasBanner'     => isset($hasBanner) ? (bool)$hasBanner : false,
                'productPager'  => $productPager,
            ]) // фильтры ?>
        <? endif ?>


        <? if ($category->isV2() || $category->getAvailableForSwitchingViews() || $category->getChosenView() === \Model\Product\Category\BasicEntity::VIEW_EXPANDED): ?>
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

    <?= $helper->render('product/__list', ['listViewData' => $listViewData]) ?>

    <? if ($category->isV2()): ?>
        <div class="sorting clearfix js-category-sortingAndPagination">
            <?= $helper->render('product-category/v2/__pagination', ['pager' => $productPager, 'category' => $category]) // листалка ?>
        </div>
    <? else: ?>
        <div class="bSortingLine mPagerBottom clearfix js-category-sortingAndPagination">
            <?= $helper->render('product/__pagination', ['pager' => $productPager, 'category' => $category]) // листалка ?>
        </div>
    <? endif ?>

    <? else : ?>
    </div>
    <? endif ?>

    <!-- Промокаталог Tchibo в листинге -->
    <? if((bool)$slideData && $category->isTchibo()): ?>
        <?= $helper->render('tchibo/promo-catalog', ['slideData' => $slideData, 'categoryToken' => $category->getRoot() ? $category->getRoot()->getToken() : '']) // promo slider ?>
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