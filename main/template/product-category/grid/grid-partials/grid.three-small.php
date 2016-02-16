<?
use Model\Product\Category\Entity as Category;
/**
 * @var $page \View\ClosedSale\SaleIndexPage
 * @var Category[] $categories
 */
?>
<!--
    Строка с тремя ячейками, выста каждой ячейки 220 пиксел
    Модификатор grid-3cell cell-h-220
 -->
<div class="s-sales-grid__row grid-3cell cell-h-220">
    <? foreach ($categories as $category) : ?>
        <?= $page->render('product-category/grid/grid-partials/item', ['category' => $category, 'imageType' => Category::MEDIA_GRID_SMALL]) ?>
    <? endforeach ?>
</div>
