<?
use Model\ClosedSale\ClosedSaleEntity;
/**
 * @var $page \View\ClosedSale\SaleIndexPage
 * @var ClosedSaleEntity[] $sales
 */
?>
<!--
    Строка с тремя ячейками, выста каждой ячейки 220 пиксел
    Модификатор grid-3cell cell-h-220
 -->
<div class="s-sales-grid__row grid-3cell cell-h-220">
	<? foreach ($sales as $sale) : ?>
	<?= $page->render('closed-sale/partials/sale', ['sale' => $sale, 'imageType' => ClosedSaleEntity::MEDIA_SMALL]) ?>
	<? endforeach ?>
</div>
