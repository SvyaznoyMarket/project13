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


$tagCategoryTokens = ['tagToken' => $tag->getToken(), 'categoryToken' => $category->getToken()];

// дочерние категории для тегов:
$categoriesLinks = [];
foreach ( $categories as $child ) {
    $categoriesLinks[] = [
        'name' => $child->getName(),
        'url' => $page->url('tag.category', $tagCategoryTokens),
        'image' => $child->getImageUrl(),
        'active' => ( $child->getId() === $selectedCategory->getId() ) ? true : false,
    ];
}

?>
<div class="bCatalog">
    <h1 class="bTitlePage"><?= $pageTitle ?></h1>
    <?= $helper->renderWithMustache('product-category/_listInFilter', ['links' => $categoriesLinks]) // дочерние категории для тегов ?>

    <?= $helper->render('product-category/__filter', [
        'baseUrl'       => $helper->url('tag.category', $tagCategoryTokens),
        'countUrl'      => $helper->url('tag.category', $tagCategoryTokens),
        'productFilter' => $productFilter,
        'hotlinks'      => $hotlinks,
        'openFilter'    => false,
    ]) // фильтры ?>

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