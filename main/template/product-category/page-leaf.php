<?php
/**
 * @var $page                   \View\ProductCategory\LeafPage
 * @var $category               \Model\Product\Category\Entity
 * @var $brand                  \Model\Brand\Entity|null
 * @var $productFilter          \Model\Product\Filter
 * @var $productPager           \Iterator\EntityPager
 * @var $productSorting         \Model\Product\Sorting
 * @var $productView            string
 * @var $productVideosByProduct array
 * @var $hotlinks               array
 * @var $seoContent             string
 * @var $relatedCategories      array
 * @var $categoryConfigById     array
 */
?>

<?
    $helper = new \Helper\TemplateHelper();
    if ($productFilter->getShop()) $page->setGlobalParam('shop', $productFilter->getShop());

    // получаем стиль листинга
    $listingStyle = !empty($catalogJson['listing_style']) ? $catalogJson['listing_style'] : null;

    // получаем promo стили
    $promoStyle = 'jewel' === $listingStyle && isset($catalogJson['promo_style']) ? $catalogJson['promo_style'] : [];
?>

<div class="bCatalog<?= 'jewel' === $listingStyle ? ' mCustomCss' : '' ?>" id="bCatalog" data-lastpage="<?= $productPager->getLastPage() ?>">

    <?= $helper->render('product-category/__breadcrumbs', ['category' => $category, 'isBrand' => isset($brand)]) // хлебные крошки ?>

    <div class="bCustomFilter"<? if(!empty($promoStyle['promo_image'])): ?> style="<?= $promoStyle['promo_image'] ?>"<? endif ?>>
        <h1 class="bTitlePage"<? if(!empty($promoStyle['title'])): ?> style="<?= $promoStyle['title'] ?>"<? endif ?>><?= $title ?></h1>

        <? if (\App::config()->adFox['enabled']): ?>
        <!-- Баннер --><div id="adfox683sub" class="adfoxWrapper bBannerBox"></div><!--/ Баннер -->
        <? endif ?>

        <? if (!empty($promoContent)): ?>
            <?= $promoContent ?>
        <? elseif ($productPager->getLastPage() > 1): ?>
            <?= $helper->render('product-category/__children',
                [
                    'category'           => $category,
                    'promoStyle'         => $promoStyle,
                    'relatedCategories'  => $relatedCategories,
                    'categoryConfigById' => $categoryConfigById,
                ]
            ) // дочерние категории and relatedCategories ?>
        <? endif ?>

        <?= $helper->render('product-category/__filter', [
            'baseUrl'       => $helper->url('product.category', ['categoryPath' => $category->getPath()]),
            'countUrl'      => $helper->url('product.category.count', ['categoryPath' => $category->getPath()]),
            'productFilter' => $productFilter,
            'hotlinks'      => $hotlinks,
            'openFilter'    => false,
            'promoStyle'    => $promoStyle,
        ]) // фильтры ?>
        
        <?= $helper->render('product/__smartChoice', ['smartChoiceProducts' => $smartChoiceProducts]); ?>

        <?= $helper->render('product/__listAction', [
            'pager'          => $productPager,
            'productSorting' => $productSorting,
        ]) // сортировка, режим просмотра, режим листания ?>
    </div>

    <?= $helper->render('product/__list', [
        'pager'                  => $productPager,
        'view'                   => $productView,
        'productVideosByProduct' => $productVideosByProduct,
        'bannerPlaceholder'      => !empty($catalogJson['bannerPlaceholder']) && 'jewel' !== $listingStyle ? $catalogJson['bannerPlaceholder'] : [],
        'listingStyle'           => $listingStyle,
    ]) // листинг ?>

    <div class="bSortingLine mPagerBottom clearfix">
        <?= $helper->render('product/__pagination', ['pager' => $productPager]) // листалка ?>
    </div>

    <? if(!empty($seoContent)): ?>
        <div class="bSeoText">
            <?= $seoContent ?>
        </div>
    <? endif ?>
</div>