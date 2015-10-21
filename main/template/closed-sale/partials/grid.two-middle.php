<?
use Model\ClosedSale\ClosedSaleEntity;
/**
 * @var $page \View\ClosedSale\SaleIndexPage
 * @var ClosedSaleEntity[] $sales
 */
?>
<!--
	Строка с двумя ячейками, выста каждой ячейки 340 пиксел
	Модификатор grid-2cell cell-h-340
 -->
<div class="s-sales-grid__row grid-2cell cell-h-340">
    <? foreach ($sales as $sale) :?>
		<?= $page->render('closed-sale/partials/sale', ['sale' => $sale, 'imageType' => ClosedSaleEntity::MEDIA_MEDIUM]) ?>
	<? endforeach ?>
</div>
