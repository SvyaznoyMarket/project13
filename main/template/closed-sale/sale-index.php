<?
/**
 * @var \View\ClosedSale\SaleIndexPage $page
 */
?>
<section class="s-sales">
	<h1 class="s-sales__title">Secret Sale</h1>

	<div class="s-sales-head">
		<h2 class="s-sales-head__title">Новые акции</h2>
		<div class="s-sales-head__counter">1 день 15:04:02</div>
	</div>

	<!-- Сетка скидочных категорий -->
	<div class="s-sales-grid">

		<?= $page->render('closed-sale/grid.logic', ['currentSales' => $page->getSales()]) ?>

		<!--END Конец строки -->
	</div>
	<!--END Сетка скидочных категорий -->

	<div class="s-sales-head">
		<h2 class="s-sales-head__title">Заканчиваются сегодня</h2>
		<div class="s-sales-head__counter">15:04:02</div>
	</div>

	<!-- Сетка скидочных категорий -->
	<div class="s-sales-grid">
		<?= $page->render('closed-sale/grid.logic', ['currentSales' => $page->getSales(false)]) ?>
	</div>
	<!--END Сетка скидочных категорий -->
</section>


