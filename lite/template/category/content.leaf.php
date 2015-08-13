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

            <div class="search-result-status">
                <div class="search-result-status__title">
                    Вы искали <span class="link-color">poiakufjhskfglIE7F</span>
                </div>

                <div class="search-result-status-notfound">
                    <div class="search-result-status-notfound__title">Товары не найдены</div>
                    <div class="search-result-status-notfound__text mb-15">Попробуйте изменить поисковый запрос</div>
                    <div class="search-result-status-notfound__text">Или позвоните нам по телефону <span class="search-result-status-notfound__phone">+7 495 775-00-06</span></div>
                    <div class="search-result-status-notfound__text">мы поможем подобрать товар</div>
                </div>

                <div class="search-result-status__title">
                    Показаны результаты поиска по запросу <span class="link-color">смартфон</span>
                </div>

                <div class="search-result-status__text">По запросу <span class="link-color">ываыаыа</span> товары не найдены</div>
                <div class="search-result-status__text">Попробуйте изменить поисковый запрос</div>
            </div>

            <?= $page->render('category/_breadcrumbs', ['category' => $category]) ?>

            <!-- Breadcrumbs -->
            <? if (isset($slice)) : ?>
                <ul class="bread-crumbs"><li class="bread-crumbs__item"><?= $slice->getName() ?></li></ul>
            <? endif ?>

            <? if (count($category->getChild()) > 1) : ?>
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

        <?= $page->render('category/_filters',[
            'baseUrl'       => $helper->url('product.category', ['categoryPath' => $category->getPath()]),
            'productFilter' => $productFilter,
            'openFilter'    => false,
            'promoStyle'    => $promoStyle,
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