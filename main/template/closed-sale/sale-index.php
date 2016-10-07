<?

use \Model\ClosedSale\ClosedSaleEntity as Sale;
/**
 * @var \View\ClosedSale\SaleIndexPage $page
 */

$newSales = $page->getSales();
$oldSales = $page->getSales(false);

// Определим акцию с наименьшим сроком действия из "новых"
if ($newSales) {
	$newSaleWithMinEndDate = array_reduce(
		$newSales,
		function(Sale $carry, Sale $sale){ return $carry->endsAt < $sale->endsAt ? $carry : $sale; },
		$newSales[0]
	);
}

?>

<section class="s-sales">
	<h1 class="s-sales__title">Секретная распродажа</h1>

	<? if ($newSales) : ?>

	<div class="s-sales-head">
		<h2 class="s-sales-head__title">Новые акции</h2>
		<div class="s-sales-head__counter js-countdown-out js-countdown"
			 data-expires="<?= $newSaleWithMinEndDate->endsAt->format('U') ?>"><?= $newSaleWithMinEndDate->getDateDiffString() ?></div>
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

	<? if (!$newSales && !$oldSales) : ?>
		<div class="s-sales-noty">
			<p class="s-sales-noty__paragraph">Сейчас нет активных акций.</p>
		</div>

		<div class="s-sales-noty">
			<p class="s-sales-noty__paragraph">Но у нас еще много интересных товаров по привлекательным ценам.<br />Вы наверняка найдете то, что ищете!</p>
		</div>

		<div class="s-sales-noty">
			<p class="s-sales-noty__paragraph">Посмотреть <a class="link" href="<?= $page->url('slice', ['sliceToken' => 'all_labels']) ?>">все товары по супер-ценам</a>,
				посмотреть <a class="link" href="<?= $page->url('content', ['token' => 'special_offers']) ?>">все акции</a>
				или вернуться на <a class="link" href="<?= $page->url('homepage') ?>">главную страницу</a>,<br/>
				где вы найдете персональные рекомендации и популярные товары.</p>
		</div>

		<div class="s-sales-noty s-sales-noty_footnote">
			<p class="s-sales-noty__paragraph">Если вы считаете, что это ошибка, попробуйте <a class="dotted" href="<?= $page->url('sale.all') ?>">обновить страницу</a></p>
		</div>
	<? endif ?>

</section>


