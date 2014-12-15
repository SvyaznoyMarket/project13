<?php
/**
 * @var \View\ProductCategory\LeafPage $page
 * @var \Model\Product\Filter $productFilter
 * @var \Iterator\EntityPager $productPager
 * @var \Model\Product\Sorting $productSorting
 * @var array $productVideosByProduct
 * @var int $columnCount
 */

$helper = new \Helper\TemplateHelper();
?>

<div class="bCatalog" id="bCatalog" data-lastpage="<?= $productPager->getLastPage() ?>">
    <div class="bCustomFilter">
        <h1>ПОДАРКИ!</h1>

        <?= $helper->render('gift/category/__filter', [
            'productFilter' => $productFilter,
            'baseUrl'       => $helper->url('product.gift'),
        ]) ?>
    </div>

    <div class="sorting sorting-top clearfix js-category-sortingAndPagination">
        <?= $helper->render('gift/category/__sorting', ['productSorting' => $productSorting]) ?>
        <?= $helper->render('gift/category/__pagination', ['pager' => $productPager]) ?>
    </div>

    <?= $helper->render('product/__list', [
        'pager'                  => $productPager,
        'view'                   => 'light_with_bottom_description',
        'productVideosByProduct' => $productVideosByProduct,
        'bannerPlaceholder'      => [],
        'listingStyle'           => null,
        'columnCount'            => $columnCount,
    ]) ?>

    <div class="sorting clearfix js-category-sortingAndPagination">
        <?= $helper->render('gift/category/__pagination', ['pager' => $productPager]) ?>
    </div>

    <? if (\App::config()->product['pullRecommendation'] && \App::config()->product['viewedEnabled']): ?>
        <?= $helper->render('product/__slider', [
            'type'      => 'viewed',
            'title'     => 'Вы смотрели',
            'products'  => [],
            'count'     => null,
            'limit'     => \App::config()->product['itemsInSlider'],
            'page'      => 1,
            'url'       => $page->url('product.recommended'),
            'sender'    => [
                'name'     => 'enter',
                'position' => 'Viewed',
            ],
        ]) ?>
    <? endif ?>
</div>