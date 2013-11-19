<?php
/**
 * @var $page               \View\Tag\IndexPage
 * @var $productPager       \Iterator\EntityPager
 * @var $productSorting     \Model\Product\Sorting
 * @var $productView        string
 * @var $category           \Model\Product\Category\Entity
 * @var $categories         \Model\Product\Category\Entity[]
 * @var $selectedCategory   \Model\Product\Category\Entity
 * @var $pageTitle          string
 * @var $tag                \Model\Tag\Entity
 */

$helper = new \Helper\TemplateHelper();
$tagCategoryTokens = null;
$categoriesLinks = []; // дочерние категории для тегов:
$hotlinks = [];

$tagCategoryTokens = ['tagToken' => $tag->getToken()];

$filtersParams = [
    'productFilter'     => $productFilter,
    'hotlinks'          => $hotlinks,
    'categories'        => $categories,
    'selectedCategory'  => $selectedCategory,
    'openFilter'        => true,
    'countUrl'          => null,
    'baseUrl'           => $helper->url('tag', $tagCategoryTokens),
];

//$filterParams['countUrl'] = $helper->url('tag.category.count', $tagCategoryTokens); // <- TODO


// подкатегории для тегов:
foreach ( $categories as $subCategory ) {
    /** @var $subCategory \Model\Product\Category\Entity */

    $tagCategoryTokens['categoryToken'] = $subCategory->getToken();

    $categoriesLinks[] = [
        'name'      => $subCategory->getName(),
        'url'       => $page->url('tag.category', $tagCategoryTokens),
        'image'     => $subCategory->getImageUrl(),
        'active'    => ( $selectedCategory && $subCategory->getId() === $selectedCategory->getId() ) ? true : false,
    ];
}

?>
<div class="bCatalog">
    <h1 class="bTitlePage"><?= $pageTitle ?></h1>
    <? /*if (\App::config()->adFox['enabled']): ?>
        <!-- Баннер --><div id="adfox683sub" class="adfoxWrapper bBannerBox"></div><!--/ Баннер -->
    <? endif */?>
    <?
    if (!empty($categoriesLinks)) {
        echo $helper->renderWithMustache('product-category/_listInFilter', ['links' => $categoriesLinks]); // дочерние категории для тегов
    }
    ?>

    <?= $helper->render('product-category/__filter', $filtersParams); // фильтры ?>

    <?=
    $helper->render( 'product/__listAction', [
        'pager' => $productPager,
        'productSorting' => $productSorting,
    ] ) // сортировка, режим просмотра, режим листания
    ?>

    <?=
    $helper->render( 'product/__list', [
        'pager' => $productPager,
        'view' => $productView,
        'productVideosByProduct' => [], //$productVideosByProduct,
        'bannerPlaceholder' => !empty($bannerPlaceholder) ? $bannerPlaceholder : [],
    ] ) // листинг
    ?>

    <div class="bSortingLine mPagerBottom clearfix">
        <?= $helper->render('product/__pagination', ['pager' => $productPager]) // листалка ?>
    </div>

    <? if(!empty($seoContent)): ?>
        <div class="bSeoText">
            <?= $seoContent ?>
        </div>
    <? endif ?>

</div>