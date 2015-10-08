<?

$products = $page->getParam('products', []);
$productPager = new \Iterator\EntityPager($products, count($products));
$productView = '1';
$helper = \App::closureTemplating();
?>

<section class="s-sales">
	<header class="s-sales-header">
		<h1 class="s-sales-header__title">Распродажа Диванов</h1>

		<div class="s-sales-header-counter">
			<div class="s-sales-header-counter__time">1 день 22:45:12</div>
			<div class="s-sales-header-counter__avail">Осталось 35 товаров</div>
		</div>
	</header><!-- /header -->
	
	<!-- Все товары распроданы -->
	<div class="s-sales-noty">
		<p class="s-sales-noty__paragraph">Все товары акции распроданы. Однако, у нас есть для вас еще много интересных предложений!</p>
	</div>
	
	<!-- Отсутствуют акционные товары в выбранном регионе -->
	<div class="s-sales-noty">
		<p class="s-sales-noty__paragraph">Товары акции не доставляются в выбранный регион <a href="" class="dotted">Набережные Челны</a></p>
		<p class="s-sales-noty__paragraph">Выберите другой регион или посмотрите другие наши предложения.</p>
	</div>
	
	<!-- Акция завершина -->
	<div class="s-sales-noty">
		<p class="s-sales-noty__paragraph">Акция завершена. Однако, у нас есть для вас еще много интересных предложений!</p>
	</div>

	<? if ($products) : ?>
		<? $helper->render('product-category/v2/__listAction',
			[
				'productSorting'	=> new \Model\Product\Sorting(),
				'pager'				=> $productPager
			]) ?>

		<? $helper->render('product/__list', [
			'pager'                  => $productPager,
			'view'                   => $productView,
			'bannerPlaceholder'      => !empty($catalogJson['bannerPlaceholder']) && 'jewel' !== $listingStyle ? $catalogJson['bannerPlaceholder'] : [],
	//		'listingStyle'           => $listingStyle,
	//		'columnCount'            => isset($columnCount) ? $columnCount : 4,
	//		'class'                  => $category->isV2Furniture() && \Session\AbTest\AbTest::isNewFurnitureListing() ? 'lstn-btn2' : '',
	//		'category'               => $category,
	//		'favoriteProductsByUi'   => $favoriteProductsByUi,
	//		'cartButtonSender'       => $category->getSenderForGoogleAnalytics(),
		]) ?>
	<? endif ?>

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
		<a href="" class="button button_action button_size-l">Посмотреть текущие акции</a>
	</div>
</section>

<div class="popup popup_default popup_960 sales-noty">
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
			<a href="" class="button button_action button_size-l">Посмотреть текущие акции</a>
		</div>
	</div>
</div>