<?
/**
 * @var \View\ClosedSale\SaleIndexPage $page
 */

$newSales = $page->getSales();
$oldSales = $page->getSales(false);

?>

<section class="s-sales">
	<h1 class="s-sales__title">Secret Sale</h1>

	<? if ($newSales) : ?>

	<div class="s-sales-head">
		<h2 class="s-sales-head__title">Новые акции</h2>
		<div class="s-sales-head__counter js-countdown-out js-countdown"
			 data-expires="<?= $newSales[0]->endsAt->format('U') ?>"><?= $newSales[0]->getDateDiffString() ?></div>
	</div>

	<!-- Сетка скидочных категорий -->
	<div class="s-sales-grid">

		<?= $page->render('closed-sale/grid.logic', ['currentSales' => $newSales]) ?>

		<!--END Конец строки -->
	</div>
	<!--END Сетка скидочных категорий -->
	<? endif ?>

	<? if ($oldSales) : ?>
	<div class="s-sales-head">
		<h2 class="s-sales-head__title">Заканчиваются сегодня</h2>
		<div class="s-sales-head__counter js-countdown-out js-countdown"
			 data-expires="<?= $oldSales[0]->endsAt->format('U') ?>"><?= $oldSales[0]->getDateDiffString() ?></div>
	</div>

	<!-- Сетка скидочных категорий -->
	<div class="s-sales-grid">
		<?= $page->render('closed-sale/grid.logic', ['currentSales' => $oldSales]) ?>
	</div>
	<!--END Сетка скидочных категорий -->
	<? endif ?>

</section>


