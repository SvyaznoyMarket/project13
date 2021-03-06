<?php
/**
 * @var \View\ProductCategory\LeafPage $page
 * @var \Model\Product\Filter $productFilter
 * @var \Iterator\EntityPager $productPager
 * @var \Model\Product\Sorting $productSorting
 * @var array $listViewData
 */

$helper = new \Helper\TemplateHelper();
?>

<div class="bCatalog js-catalog js-gift-category newLayout" id="bCatalog" data-lastpage="<?= $productPager->getLastPage() ?>" data-page="<?= $productPager->getPage() ?>">
    <div class="bCustomFilter">

        <?= $helper->render('gift/category/__filter', [
            'productFilter' => $productFilter,
        ]) ?>
    </div>

    <div class="sorting sorting-top clearfix js-category-sortingAndPagination">
        <?= $helper->render('gift/category/__sorting', ['productSorting' => $productSorting]) ?>
        <?= $helper->render('gift/category/__pagination', ['pager' => $productPager]) ?>
    </div>

    <div class="js-gift-category-listing">
        <?= $helper->render('product/__list', ['listViewData' => $listViewData]) ?>
    </div>

    <div class="sorting clearfix js-category-sortingAndPagination">
        <?= $helper->render('gift/category/__pagination', ['pager' => $productPager]) ?>
    </div>

    <? if (\App::config()->product['pullRecommendation'] && \App::config()->product['viewedEnabled']): ?>
        <?= $helper->render('product/__slider', [
            'type'      => 'viewed',
            'title'     => 'Вы смотрели',
            'products'  => [],
            'limit'     => \App::config()->product['itemsInSlider'],
            'page'      => 1,
            'url'       => $page->url('product.recommended'),
            'sender'    => [
                'name'     => 'enter',
                'position' => 'Viewed',
                'from'     => 'categoryPage'
            ],
        ]) ?>
    <? endif ?>
</div>