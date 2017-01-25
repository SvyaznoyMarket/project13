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

<div class="bCatalog js-catalog <? if ($category->isV3()): ?>bCatalog-custom<? endif ?>" id="bCatalog" data-lastpage="<?= $productPager->getLastPage() ?>" data-page="<?= $productPager->getPage() ?>">
    <? if ($category->isTchibo()) : ?>
        <?= $helper->render('product-category/__sibling-list', ['categories' => $siblingCategories, 'currentCategory'    => $category,
            'rootCategoryInMenu' => $rootCategoryInMenu]) ?>
    <? else : ?>
        <?= $helper->renderWithMustache('_breadcrumbs', ['links' => $breadcrumbs]) ?>
    <? endif ?>

    <? if ($category->isAutoGrid()): ?>
        <div class="bCustomFilter">
            <? if ($category->showTitle) : ?>
                <? if ($titleBackgroundStyle) : ?>
                    <h1 class="bTitlePage_tchibo js-pageTitle" style="<?= $titleBackgroundStyle ?>"><?= $title ?></h1>
                <? else : ?>
                    <h1 class="bTitlePage js-pageTitle"><?= $title ?></h1>
                <? endif ?>
            <? endif ?>

            <? if (\App::config()->adFox['enabled']): ?>
                <div id="adfox683" class="adfoxWrapper bBannerBox"></div>
            <? endif ?>

            <? if($slideData && !$category->isTchibo()): ?>
                <?= $helper->render('tchibo/promo-catalog', ['slideData' => $slideData, 'categoryToken' => $category->getRoot() ? $category->getRoot()->getToken() : '']) // promo slider ?>
            <? endif ?>

            <? if (!empty($category->catalogJson['show_branch_menu'])) : ?>
                <?= $helper->render('_branch', ['category' => $category]) ?>
            <? endif ?>
            
            <?= $page->render('product-category/grid/grid-category', ['categories' => $category->getChild()]) ?>
        </div>
    <? elseif ($category->categoryView === 'contentPage'): ?>
        <div class="bCustomFilter">
            <? if ($category->showTitle) : ?>
                <? if ($titleBackgroundStyle) : ?>
                    <h1 class="bTitlePage_tchibo js-pageTitle" style="<?= $titleBackgroundStyle ?>"><?= $title ?></h1>
                <? else : ?>
                    <h1 class="bTitlePage js-pageTitle"><?= $title ?></h1>
                <? endif ?>
            <? endif ?>

            <? if (\App::config()->adFox['enabled']): ?>
                <div id="adfox683" class="adfoxWrapper bBannerBox"></div>
            <? endif ?>

            <? if($slideData && !$category->isTchibo()): ?>
                <?= $helper->render('tchibo/promo-catalog', ['slideData' => $slideData, 'categoryToken' => $category->getRoot() ? $category->getRoot()->getToken() : '']) // promo slider ?>
            <? endif ?>

            <? if (!empty($category->catalogJson['show_branch_menu'])) : ?>
                <?= $helper->render('_branch', ['category' => $category]) ?>
            <? endif ?>

            <? if (!empty($promoContent)): ?>
                <?= $promoContent ?>
            <? endif ?>
        </div>
    <? else: ?>
        <div class="bCustomFilter">
            <? if ($category->showTitle) : ?>
                <? if ($titleBackgroundStyle) : ?>
                    <h1 class="bTitlePage_tchibo js-pageTitle" style="<?= $titleBackgroundStyle ?>"><?= $title ?></h1>
                <? else : ?>
                    <h1 class="bTitlePage js-pageTitle"><?= $title ?></h1>
                <? endif ?>
            <? endif ?>

            <? if (\App::config()->adFox['enabled']): ?>
                <div id="adfox683sub" class="adfoxWrapper bBannerBox"></div>
            <? endif ?>

            <? if($slideData && !$category->isTchibo()): ?>
                <?= $helper->render('tchibo/promo-catalog', ['slideData' => $slideData, 'categoryToken' => $category->getRoot() ? $category->getRoot()->getToken() : '']) // promo slider ?>
            <? endif ?>

            <? if (!empty($category->catalogJson['show_branch_menu'])) : ?>
                <?= $helper->render('_branch', ['category' => $category, 'isBranchPage' => true]) ?>
            <? endif ?>

            <? if (!empty($promoContent)): ?>
                <? if ($category->isV2()): ?>
                    <div style="margin-left: -10px;"><?= $promoContent ?></div>
                <? else: ?>
                    <?= $promoContent ?>
                <? endif ?>
            <? else : ?>
                <?= $helper->render('product-category/__children',
                    [
                        'category'           => $category,
                        'relatedCategories'  => $relatedCategories,
                        'categoryConfigById' => $categoryConfigById,
                        'productPager'       => $productPager,
                        'showFullChildren'   => $category->isShowFullChildren(),
                    ]
                ) // дочерние категории and relatedCategories ?>
            <? endif ?>

            <? if ($category->isShowSmartChoice() && isset($smartChoiceProducts)): ?>
                <?= $helper->render('product/__smartChoice', ['smartChoiceProducts' => $smartChoiceProducts]); ?>
            <? endif ?>

            <? if ($category->isV2()): ?>
                <?= $helper->render('product-category/v2/__filter', [
                    'productFilter' => $productFilter,
                    'category'      => $category,
                ]) // фильтры ?>
            <? elseif ($category->isV3()): ?>
                <?= $helper->render('product-category/v3/__filter', [
                    'productFilter' => $productFilter,
                    'openFilter'    => false,
                ]) // фильтры ?>
            <? else: ?>
                <?= $helper->render('product-category/__filter', [
                    'productFilter' => $productFilter,
                    'openFilter'    => false,
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

            <?= $helper->render('product-category/__hotlink', ['hotlinks' => $hotlinks]) // hotlinks ?>
        </div>
    <? endif ?>

</div>