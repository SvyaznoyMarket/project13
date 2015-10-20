<?

use Model\ClosedSale\ClosedSaleEntity;
/**
 * @var \View\ClosedSale\SaleShowPage $page
 * @var \Model\ClosedSale\ClosedSaleEntity $currentSale
 * @var \Model\ClosedSale\ClosedSaleEntity[] $sales
 * @var \Model\Product\Entity $products
 * @var \Model\Product\Category\Entity[] $categories
 * @var $productPager
 * @var $productView
 * @var int $availableProductCount
 * @var \Helper\TemplateHelper $helper
 */

shuffle($sales);
// исключим текущую акцию из всех активных акций
$sales = array_filter(
	$sales,
	function (ClosedSaleEntity $sale) use ($currentSale) {
		return $sale->uid !== $currentSale->uid;
	}
);

$helper = \App::closureTemplating()->getParam('helper');
?>

<section class="s-sales">
	<header class="s-sales-header">
		<h1 class="s-sales-header__title"><?= $currentSale->name ?></h1>

		<? if ($currentSale->isActive()) : ?>
		<div class="s-sales-header-counter">
			<div class="s-sales-header-counter__time js-countdown-out js-countdown"
				 data-expires="<?= $currentSale->endsAt->format('U') ?>"><?= $currentSale->getDateDiffString() ?></div>
			<div class="s-sales-header-counter__avail">Осталось <?= $helper->numberChoiceWithCount($availableProductCount, ['товар', 'товара', 'товаров'])?></div>
		</div>
		<? endif ?>
	</header><!-- /header -->

	<? if ($availableProductCount === 0) : ?>
	<!-- Все товары распроданы -->
	<div class="s-sales-noty">
		<p class="s-sales-noty__paragraph">Все товары акции распроданы. Однако, у нас есть для вас еще много интересных предложений!</p>
	</div>
	<? endif ?>

	<? if (false) : ?>
	<!-- Отсутствуют акционные товары в выбранном регионе -->
	<div class="s-sales-noty">
		<p class="s-sales-noty__paragraph">Товары акции не доставляются в выбранный регион <a href="" class="dotted">Набережные Челны</a></p>
		<p class="s-sales-noty__paragraph">Выберите другой регион или посмотрите другие наши предложения.</p>
	</div>
	<? endif ?>

	<? if (!$currentSale->isActive()) : ?>
	<!-- Акция завершeна -->
	<div class="s-sales-noty">
		<p class="s-sales-noty__paragraph">Акция завершена. Однако, у нас есть для вас еще много интересных предложений!</p>
	</div>
	<? endif ?>

	<? if ($products && $availableProductCount > 0 && $currentSale->isActive()) : ?>

		<? if (count((array)$categories) > 1) : ?>
		<!-- Категории -->
		<ul class="list-categories">
			<? foreach ($categories as $category) : ?>
			<li class="list-categories__item">
				<a class="list-categories__link" href="<?= $page->url('sale.one', ['uid' => $currentSale->uid, 'categoryId' => $category->id]) ?>">
					<span class="list-categories__image"><img class="image" src="<?= $category->getMediaSource('category_96x96')->url ?>" /></span>
					<span class="list-categories__name"><?= $category->getName() ?></span>
				</a>
			</li>
			<? endforeach ?>
		</ul>
		<? endif ?>

		<?= $helper->render( 'product/__listAction', [
			'pager' => $productPager,
			'productSorting' => $productSorting,
		] ) // сортировка, режим просмотра, режим листания ?>

		<?= $helper->render('product/__list', [
			'pager' => $productPager,
			'view' => $productView,
			'bannerPlaceholder' => !empty($bannerPlaceholder) ? $bannerPlaceholder : [],
			'cartButtonSender'	=> [
				'name'		=> 'secret_sale',
				'position'	=> 'listing'
			]
		]) // листинг ?>

		<div class="sorting sorting-top clearfix js-category-sortingAndPagination">
			<?= $helper->render('product/__pagination', ['pager' => $productPager]) // листалка ?>
		</div>

	<? endif ?>

	<!-- Сетка скидочных категорий -->
	<div class="s-sales-grid">
		<!--
			Строка с тремя ячейками, выста каждой ячейки 220 пиксел
			Модификатор grid-3cell cell-h-220
		 -->
		<div class="s-sales-grid__row grid-3cell cell-h-220">
		<? foreach (array_slice($sales, 0, 3) as $sale) : ?>
			<!-- Если равершина добавляем модификатор sale-finished и соостветствующий текст в атрибут data-sale-status внутри партиала -->
			<!--< div class="s-sales-grid__cell sale-finished" data-sale-status="Акция завершена"> -->

				<?= $page->render('closed-sale/partials/sale', ['sale' => $sale, 'imageType' => ClosedSaleEntity::MEDIA_SMALL]) ?>

		<? endforeach ?>
		</div>

	</div>

	<div class="button-container">
		<a href="<?= $page->url('sale.all') ?>" class="button button_action button_size-l">Посмотреть текущие акции</a>
	</div>
</section>

<div class="popup popup_default popup_960 sales-noty" style="display: none;">
	<i class="closer js-popup-closer">×</i>

	<div class="s-sales">
		<!-- Акция завершина -->
		<div class="s-sales-noty">
			<p class="s-sales-noty__paragraph">Акция завершена. Однако, у нас есть для вас еще много интересных предложений!</p>
		</div>

		<!-- Сетка скидочных категорий -->
		<div class="s-sales-grid">
			<!--
				Строка с тремя ячейками, выста каждой ячейки 220 пиксел
				Модификатор grid-3cell cell-h-220
			 -->
			<div class="s-sales-grid__row grid-3cell cell-h-220">
				<div class="s-sales-grid__cell">
					<a class="s-sales-grid__link" href="">
						<img src="http://img0.joyreactor.cc/pics/post/full/%D0%BA%D0%BE%D1%82%D0%B8%D0%BA%D0%B8-%D0%B6%D0%B8%D0%B2%D0%BD%D0%BE%D1%81%D1%82%D1%8C-wallpaper-%D0%B1%D0%B0%D1%8F%D0%BD-885065.jpeg" alt="" class="s-sales-grid__img">

						<span class="s-sales-grid-desc">
							<span class="s-sales-grid-desc__value">-70%</span>
							<span class="s-sales-grid-desc__title">
								<span class="s-sales-grid-desc__title-name">Путешествия</span>
								<span class="s-sales-grid-desc__title-duration">Конец акции 22.09.2015 00:00</span>
							</span>
						</span>
					</a>
				</div>

				<div class="s-sales-grid__cell">
					<a class="s-sales-grid__link" href="">
						<img src="http://img0.joyreactor.cc/pics/post/full/%D0%BA%D0%BE%D1%82%D0%B8%D0%BA%D0%B8-%D0%B6%D0%B8%D0%B2%D0%BD%D0%BE%D1%81%D1%82%D1%8C-wallpaper-%D0%B1%D0%B0%D1%8F%D0%BD-885065.jpeg" alt="" class="s-sales-grid__img">

						<span class="s-sales-grid-desc">
							<span class="s-sales-grid-desc__value">-70%</span>
							<span class="s-sales-grid-desc__title">
								<span class="s-sales-grid-desc__title-name">Путешествия</span>
								<span class="s-sales-grid-desc__title-duration">Конец акции 22.09.2015 00:00</span>
							</span>
						</span>
					</a>
				</div>

				<div class="s-sales-grid__cell">
					<a class="s-sales-grid__link" href="">
						<img src="http://img0.joyreactor.cc/pics/post/full/%D0%BA%D0%BE%D1%82%D0%B8%D0%BA%D0%B8-%D0%B6%D0%B8%D0%B2%D0%BD%D0%BE%D1%81%D1%82%D1%8C-wallpaper-%D0%B1%D0%B0%D1%8F%D0%BD-885065.jpeg" alt="" class="s-sales-grid__img">

						<span class="s-sales-grid-desc">
							<span class="s-sales-grid-desc__value">-70%</span>
							<span class="s-sales-grid-desc__title">
								<span class="s-sales-grid-desc__title-name">Путешествия</span>
								<span class="s-sales-grid-desc__title-duration">Конец акции 22.09.2015 00:00</span>
							</span>
						</span>
					</a>
				</div>
			</div>
			<!--END Конец строки -->
		</div>

		<div class="button-container">
			<a href="<?= $page->url('sale.all') ?>" class="button button_action button_size-l">Посмотреть текущие акции</a>
		</div>
	</div>
</div>