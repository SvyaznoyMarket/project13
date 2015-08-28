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

if (!isset($category)) $category = new Model\Product\Category\Entity([]);

?>
<!-- для внутренних страниц добавляется класс middle_transform -->
<div class="middle js-module-require"
     data-module="enter.catalog"
     data-category ='<?= json_encode([
        'lastPage'  => $productPager->getLastPage(),
        'name'      => $category->getName(),
        'url'       => $category->getLink()
     ], JSON_UNESCAPED_UNICODE) ?>'
    >
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
                <?= $page->render('category/_breadcrumbs', [
                    'category'  => $category,
                    'slice'     => isset($slice) ? $slice : null
                ]) ?>
            <? endif ?>

            <? if (isset($slice)) : ?>
                <div class="section__title section__title_h1"><?= $slice->getName() ?></div>
            <? endif ?>

            <?= $page->blockCategories() ?>

        </div>

        <hr class="hr-orange">

        <?= $page->blockFilters() ?>

        <div class="js-show-fixed-userbar"></div>

        <!-- сортировка -->
        <div class="sorting sorting-top js-category-sortingAndPagination">

            <?= $page->render('category/list/sorting', ['sorting' => $productSorting, 'helper' => $helper]) ?>

            <?= $pagerHtml ?>
        </div>
        <!--/ сортировка -->

        <div class="section">
            <div class="goods goods_grid goods_listing grid-4col js-catalog-wrapper">
                <?= $productPager->count() > 0
                    ? $page->render('category/list/pager', ['productPager'=> $productPager])
                    : $helper->renderWithMustache('category/list/empty.listing', [
                        'category' => [
                            'url' => $category->getLink(),
                            'name' => $category->getName()
                        ]
                    ]);
                ?>
            </div>
        </div>

        <div class="sorting sorting_bottom js-category-sortingAndPagination">
            <?= $pagerHtml ?>
        </div>

        <?= $page->blockViewed() ?>

        <? if(false && !empty($seoContent)): ?>
            <div class="section section_bordered section_seo">
                <?= $seoContent ?>
            </div>
        <? endif ?>
    </main>
</div>

<script type="text/plain" id="js-list-item-template">
    {{#products}}
    <?= file_get_contents(\App::config()->templateDir . '/category/list/pager.mustache') ?>
    {{/products}}
    {{^products}}
    <?= file_get_contents(\App::config()->templateDir . '/category/list/empty.listing.mustache') ?>
    {{/products}}
</script>

<script type="text/plain" id="js-pagination-template">
    <?= file_get_contents(\App::config()->templateDir . '/category/list/pagination.mustache') ?>
</script>

<script type="text/plain" id="js-list-selected-filter-template">
    <?= file_get_contents(\App::config()->templateDir . '/category/filters/selected.filters.mustache') ?>
</script>