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
 */
?>

<?

$helper = \App::helper();
$pagerHtml = $page->render('category/list/pagination', ['pager' => $productPager]);

?>
<!-- для внутренних страниц добавляется класс middle_transform -->
<div class="middle middle_transform js-module-require" data-module="enter.catalog" data-page-quantity='<?= $productPager->getLastPage() ?>'>
    <div class="container">
        <main class="content <?= isset($jewelClass) ? $jewelClass : '' ?>">
            <div class="content__inner">
                <!-- баннер -->
                <div class="banner-section">
                    <img src="http://content.adfox.ru/150713/adfox/176461/1346077.jpg" width="940" height="240" alt="" border="0">
                </div>
                <!--/ баннер -->

                <div class="section">

                    <?= $page->render('category/_breadcrumbs', ['category' => $category]) ?>

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
                </div>

                <hr class="hr-orange">

                <div class="js-show-fixed-userbar"></div>

                <?= $page->render('category/_filters',[
                    'baseUrl'       => $helper->url('product.category', ['categoryPath' => $category->getPath()]),
                    'productFilter' => $productFilter,
                ]) ?>

                <?= $helper->renderWithMustache('category/filters/selected.filters') ?>

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

                <!-- SEO информация -->
                <div class="section section_bordered section_seo">
                    <p>Тут какой-то SEO-текст</p>
                </div>
                <!--/ SEO информация -->
            </div>
        </main>
    </div>

    <aside class="left-bar">
        <?= $page->blockNavigation() ?>
    </aside>
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