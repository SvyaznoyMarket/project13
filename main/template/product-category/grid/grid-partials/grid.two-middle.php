<?
use Model\Product\Category\Entity as Category;
/**
 * @var $page \View\ClosedSale\SaleIndexPage
 * @var Category[] $categories
 */
?>
<!--
	Строка с двумя ячейками, выста каждой ячейки 340 пиксел
	Модификатор grid-2cell cell-h-340
 -->
<div class="s-sales-grid s-sales-grid--category">
    <div class="s-sales-grid__row cell-h-340 grid-2cell">
        <? foreach ($categories as $category) :?>
            <?= $page->render('product-category/grid/grid-partials/item', ['category' => $category, 'imageType' => Category::MEDIA_GRID_MEDIUM]) ?>
        <? endforeach ?>
    </div>
</div>
