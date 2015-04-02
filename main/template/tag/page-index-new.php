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

$tagCategoryTokens = ['tagToken' => $tag->getToken()];

// подкатегории для тегов:
foreach ( $categories as $subCategory ) {
    /** @var $subCategory \Model\Product\Category\Entity */

    $categoriesLinks[] = [
        'name'      => $subCategory->getName(),
        'url'       => $page->url('tag.category', array_merge( $tagCategoryTokens, ['categoryToken' => $subCategory->getToken()] )),
        'image'     => $subCategory->getImageUrl(),
        'active'    => ( $selectedCategory && $subCategory->getId() === $selectedCategory->getId() ) ? true : false,
    ];
}

?>
<div class="bCatalog" id="bCatalog" data-lastpage="<?= $productPager->getLastPage() ?>">
    <h1 class="bTitlePage js-pageTitle"><?= $pageTitle ?></h1>
    <? /*if (\App::config()->adFox['enabled']): ?>
        <!-- Баннер --><div id="adfox683sub" class="adfoxWrapper bBannerBox"></div><!--/ Баннер -->
    <? endif */?>
    <?
    if ( !$selectedCategory && !empty($categoriesLinks) && $productPager->getLastPage() > 1 ) {
        echo $helper->renderWithMustache('product-category/_listInFilter', ['links' => $categoriesLinks]); // дочерние категории для тегов
    }
    ?>

    <?= $helper->render('product-category/__filter', [
        'productFilter'     => $productFilter,
        'categories'        => $categories,
        'openFilter'        => true,
        'baseUrl'           => $helper->url('tag', $tagCategoryTokens),
    ]); // фильтры ?>

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
        'bannerPlaceholder' => !empty($bannerPlaceholder) ? $bannerPlaceholder : [],
    ] ) // листинг
    ?>

    <div class="bSortingLine mPagerBottom clearfix js-category-sortingAndPagination">
        <?= $helper->render('product/__pagination', ['pager' => $productPager]) // листалка ?>
    </div>
</div>