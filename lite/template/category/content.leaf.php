<?
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
 * @var $slice                  \Model\Slice\Entity
 */
?>

<?

$helper = \App::helper();
$pagerHtml = $page->render('category/list/pagination', ['pager' => $productPager]);
$promoStyle = 'jewel' === $listingStyle && isset($catalogJson['promo_style']) ? $catalogJson['promo_style'] : [];

?>
<!-- для внутренних страниц добавляется класс middle_transform -->
<div class="middle js-module-require" data-module="enter.catalog" data-page-quantity='<?= $productPager->getLastPage() ?>'>
    <main class="content <?= isset($jewelClass) ? $jewelClass : '' ?>">
        <!-- баннер -->
        <div class="banner-section" style="display: none">
            <img src="" width="940" height="240" alt="" border="0">
        </div>
        <!--/ баннер -->

        <div class="section">

            <?= $page->blockSearch() ?>

            <!-- Breadcrumbs -->
            <? if ($category) : ?>
                <?= $page->render('category/_breadcrumbs', ['category' => $category]) ?>
            <? endif ?>

            <? if (isset($slice)) : ?>
                <ul class="bread-crumbs"><li class="bread-crumbs__item"><?= $slice->getName() ?></li></ul>
            <? endif ?>

            <? if ($category && count($category->getChild()) > 1) : ?>
                <ul class="categories-grid grid-3col">
                    <? foreach ($category->getChild() as $childCategory) : ?>
                        <li class="categories-grid__item grid-3col__item">
                            <a href="<?= $childCategory->getLink() ?>" class="categories-grid__link">
                                <span class="categories-grid__img">
                                    <img src="<?= $childCategory->getImageUrl() ?>" alt="" class="image">
                                </span>

                                <span class="categories-grid__text"><?= $childCategory->getName() ?></span>
                            </a>
                        </li>
                    <? endforeach ?>
                </ul>
            <? endif ?>

        </div>

        <hr class="hr-orange">

        <div class="js-show-fixed-userbar"></div>

        <?= $page->blockFilters() ?>

        <!-- сортировка -->
        <div class="sorting sorting-top js-category-sortingAndPagination">

            <?= $page->render('category/list/sorting', ['sorting' => $productSorting, 'helper' => $helper]) ?>

            <?= $pagerHtml ?>
        </div>
        <!--/ сортировка -->

        <div class="section">
            <div class="goods goods_grid goods_listing grid-4col js-catalog-wrapper">
                <?= $page->render('category/list/pager', ['productPager'=> $productPager]) ?>
            </div>
        </div>

        <div class="sorting sorting_bottom js-category-sortingAndPagination">
            <?= $pagerHtml ?>
        </div>

        <?= $page->blockViewed() ?>

        <? if(!empty($seoContent)): ?>
            <div class="section section_bordered section_seo">
                <?= $seoContent ?>
            </div>
        <? endif ?>
    </main>
</div>

<script type="text/plain" id="js-list-item-template">
    <?= file_get_contents(\App::config()->templateDir . '/category/list/pager.mustache') ?>
</script>

<script type="text/plain" id="js-pagination-template">
    <?= file_get_contents(\App::config()->templateDir . '/category/list/pagination.mustache') ?>
</script>

<script type="text/plain" id="js-list-selected-filter-template">
    <?= file_get_contents(\App::config()->templateDir . '/category/filters/selected.filters.mustache') ?>
</script>