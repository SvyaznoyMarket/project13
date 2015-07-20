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
<div class="middle middle_transform js-module-require" data-module="enter.catalog">
    <div class="container">
        <main class="content">
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

                <?= $page->render('category/_filters',[
                    'baseUrl'       => $helper->url('product.category', ['categoryPath' => $category->getPath()]),
                    'productFilter' => $productFilter,
                ]) ?>

                <!-- сортировка -->
                <div class="sorting sorting-top js-category-sortingAndPagination">
                    <ul class="sorting_lst fl-l js-category-sorting">
                        <li class="sorting_i sorting_i-tl">Сортировать</li>

                        <li class="sorting_i act js-category-sorting-activeItem js-category-sorting-defaultItem js-category-sorting-item">
                            <a class="sorting_lk jsSorting" data-sort="default-desc" href="/catalog/electronics/kompyuteri-i-plansheti-plansheti-817?shop=87&amp;f-prop9396-from=5&amp;f-prop3826-android_4_1_jelly_bean=29928">Автоматически</a>
                        </li>
                        <li class="sorting_i js-category-sorting-item">
                            <a class="sorting_lk jsSorting" data-sort="hits-desc" href="/catalog/electronics/kompyuteri-i-plansheti-plansheti-817?shop=87&amp;f-prop9396-from=5&amp;f-prop3826-android_4_1_jelly_bean=29928&amp;sort=hits-desc">Хиты продаж</a>
                        </li>
                        <li class="sorting_i js-category-sorting-item">
                            <a class="sorting_lk jsSorting" data-sort="price-asc" href="/catalog/electronics/kompyuteri-i-plansheti-plansheti-817?shop=87&amp;f-prop9396-from=5&amp;f-prop3826-android_4_1_jelly_bean=29928&amp;sort=price-asc">По цене ▲</a>
                        </li>
                        <li class="sorting_i js-category-sorting-item">
                            <a class="sorting_lk jsSorting" data-sort="price-desc" href="/catalog/electronics/kompyuteri-i-plansheti-plansheti-817?shop=87&amp;f-prop9396-from=5&amp;f-prop3826-android_4_1_jelly_bean=29928&amp;sort=price-desc">По цене ▼</a>
                        </li>
                    </ul>

                    <?= $pagerHtml ?>
                </div>
                <!--/ сортировка -->

                <div class="section">
                    <div class="goods goods_grid goods_listing grid-4col">
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
