<?php
/**
 * @var $page                    \View\ProductCategory\BranchPage
 * @var $category                \Model\Product\Category\Entity
 * @var $productFilter           \Model\Product\Filter
 * @var $productPagersByCategory \Iterator\EntityPager[]
 * @var $productVideosByProduct  array
 */
?>

<?
$helper = new \Helper\TemplateHelper();

$count = 0;
if ($productFilter->getShop()) $page->setGlobalParam('shop', $productFilter->getShop());
?>

<div class="bCatalog">

    <?= $helper->render('product-category/__breadcrumbs', ['category' => $category]) // хлебные крошки ?>

	<h1  class="bTitlePage"><?= $category->getName() ?></h1>

    <?= $helper->render('product-category/__children', ['category' => $category]) // дочерние категории ?>

    <?= $helper->render('product-category/__filter', ['category' => $category, 'productFilter' => $productFilter]) // фильтры ?>


	<!-- Сортировка товаров на странице -->
	<div class="bSortingLine clearfix">
		<!-- Сортировка товаров по параметрам -->
		<ul class="bSortingList mSorting">
			<li class="bSortingList__eItem mTitle">Сортировать</li>

			<li class="bSortingList__eItem mSortItem mActive"><a class="bSortingList__eLink" href="">Автоматически</a></li>
			<li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink" href="">Лидеры продаж</a></li>
			<li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink" href="">Новинки</a></li>
			<li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink" href="">Сначала недорогие</a></li>
			<li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink" href="">Сначала дорогие</a></li>
		</ul>
		<!-- /Сортировка товаров по параметрам -->

		<!-- Выбор варианта отображения списка товаров на странице -->
		<ul class="bSortingList mViewer">
			<li class="bSortingList__eItem mTitle">Вид</li>

			<li class="bSortingList__eItem mSortItem mActive"><a class="bSortingList__eLink mTable" href=""><span class="bIco mIcoTable"></span></a></li>
			<li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink mLine" href=""><span class="bIco mIcoLine"></span></a></li>
		</ul>
		<!-- /Выбор варианта отображения списка товаров на странице -->

		<!-- Выбор вывода товаров на странице страницами/простыней -->
		<ul class="bSortingList mPager">
			<li class="bSortingList__eItem mTitle">Страницы</li>

			<li class="bSortingList__eItem mSortItem mActive"><a class="bSortingList__eLink" href="">123</a></li>
			<li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink" href="">&#8734;</a></li>
		</ul>
		<!-- /Выбор вывода товаров на странице страницами/простыней -->
	</div>
	<!-- /Сортировка товаров на странице -->

	<!-- Листинг товаров -->
	<ul class="bListing clearfix">
		<!-- Элемент листинга/продукт -->
		<li class="bListingItem">
			<div class="bListingItem__eInner">
				<!-- Блок с именем продукта и иконками доп. просмотра товара -->
				<div class="bSimplyDesc">
					<p class="bSimplyDesc__eText"><a href="">Универсальный набор (инструментальный ящик) Jonnesway C-3DH201 торцевых головок 1/2" DR, 10-32 мм</a></p>
					<ul class="bSimplyDescStikers">
						<li class="bSimplyDescStikers__eItem mLeftStiker"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/stiker.png" /></li>

						<li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/video.png" /></li>
						<li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/grad360.png" /></li>
					</ul>
				</div>
				<!-- /Блок с именем продукта и иконками доп. просмотра товара -->

				<a href="" class="bProductImg"><img class="bProductImg__eImg" src="http://fs03.enter.ru/1/1/500/f3/207661.jpg" /></a>

				<div class="bPriceLine clearfix">
					<span class="bPriceOld"><strong class="bDecor">8 592</strong> <span class="rubl">p</span></span>
					<span class="bAvailible">Только в магазинах</span>
				</div>

				<div class="bPriceLine clearfix">
					<span class="bPrice"><strong>8 592</strong> <span class="rubl">p</span></span>
					<span class="bOptions"><span class="bDecor">Варианты</span></span>
				</div>

				<div class="bOptionsSection">
					<i class="bCorner"></i>
					<i class="bCornerDark"></i>

					<ul class="bOptionsList">
						<li class="bOptionsList__eItem">Цвет обивки</li>
						<li class="bOptionsList__eItem">Наличие подъемного механизма</li>
					</ul>
				</div>

				<div class="bBtnLine clearfix">
					<div class="btnBuy"><a class="btnBuy__eLink" href="">Купить</a></div>
					<a class="btnView mBtnGrey" href="">Посмотреть</a>
				</div>
			</div>
		</li>
		<!-- /Элемент листинга/продукт -->

		<li class="bListingItem">
			<div class="bListingItem__eInner">
				<div class="bSimplyDesc">
					<p class="bSimplyDesc__eText"><a href="">Универсальный набор</a></p>
				</div>

				<a href="" class="bProductImg"><img class="bProductImg__eImg" src="http://fs09.enter.ru/1/1/500/75/167449.jpg" /></a>

				<div class="bPriceLine clearfix">
					<span class="bPriceOld"><strong class="bDecor">8 592</strong> <span class="rubl">p</span></span>
					<span class="bAvailible">Только в магазинах</span>
				</div>

				<div class="bPriceLine clearfix">
					<span class="bPrice"><strong>8 592</strong> <span class="rubl">p</span></span>
				</div>

				<div class="bBtnLine clearfix">
					<div class="btnBuy"><a class="btnBuy__eLink" href="">Купить</a></div>
					<a class="btnView mBtnGrey" href="">Посмотреть</a>
				</div>
			</div>
		</li>

		<li class="bListingItem">
			<div class="bListingItem__eInner">
				<div class="bSimplyDesc">
					<p class="bSimplyDesc__eText"><a href="">Универсальный набор (инструментальный ящик)</a></p>
					<ul class="bSimplyDescStikers">
						<li class="bSimplyDescStikers__eItem mLeftStiker"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/stiker.png" /></li>

						<li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/video.png" /></li>
						<li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/grad360.png" /></li>
					</ul>
				</div>

				<a href="" class="bProductImg"><img class="bProductImg__eImg" src="http://fs03.enter.ru/1/1/500/f3/207661.jpg" /></a>

				<div class="bPriceLine clearfix">
					<span class="bPriceOld"><strong class="bDecor">8 592</strong> <span class="rubl">p</span></span>
					<span class="bAvailible">Только в магазинах</span>
				</div>

				<div class="bPriceLine clearfix">
					<span class="bPrice"><strong>8 592</strong> <span class="rubl">p</span></span>
					<span class="bOptions"><span class="bDecor">Варианты</span></span>
				</div>

				<div class="bOptionsSection">
					<i class="bCorner"></i>
					<i class="bCornerDark"></i>

					<ul class="bOptionsList">
						<li class="bOptionsList__eItem">Цвет обивки</li>
						<li class="bOptionsList__eItem">Наличие подъемного механизма</li>
					</ul>
				</div>

				<div class="bBtnLine clearfix">
					<div class="btnBuy"><a class="btnBuy__eLink" href="">Купить</a></div>
					<a class="btnView mBtnGrey" href="">Посмотреть</a>
				</div>
			</div>
		</li>

		<li class="bListingItem mLast">
			<div class="bListingItem__eInner">
				<div class="bSimplyDesc">
					<p class="bSimplyDesc__eText"><a href="">Универсальный набор (инструментальный ящик) Jonnesway C-3DH201</a></p>
					<ul class="bSimplyDescStikers">
						<li class="bSimplyDescStikers__eItem mLeftStiker"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/stiker.png" /></li>

						<li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/video.png" /></li>
						<li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/grad360.png" /></li>
					</ul>
				</div>

				<a href="" class="bProductImg"><img class="bProductImg__eImg" src="http://fs09.enter.ru/1/1/500/75/167449.jpg" /></a>

				<div class="bPriceLine clearfix">
					<span class="bPrice"><strong>8 592</strong> <span class="rubl">p</span></span>
				</div>

				<div class="bBtnLine clearfix">
					<div class="btnBuy"><a class="btnBuy__eLink" href="">Купить</a></div>
					<a class="btnView mBtnGrey" href="">Посмотреть</a>
				</div>
			</div>
		</li>

		<li class="bListingItem">
			<div class="bListingItem__eInner">
				<div class="bSimplyDesc">
					<p class="bSimplyDesc__eText"><a href="">Универсальный набор (инструментальный ящик) Jonnesway C-3DH201 торцевых головок</a></p>
					<ul class="bSimplyDescStikers">
						<li class="bSimplyDescStikers__eItem mLeftStiker"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/stiker.png" /></li>

						<li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/video.png" /></li>
						<li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/grad360.png" /></li>
					</ul>
				</div>

				<a href="" class="bProductImg"><img class="bProductImg__eImg" src="http://fs09.enter.ru/1/1/500/75/167449.jpg" /></a>

				<div class="bPriceLine clearfix">
					<span class="bPriceOld"><strong class="bDecor">8 592</strong> <span class="rubl">p</span></span>
					<span class="bAvailible">Только в магазинах</span>
				</div>

				<div class="bPriceLine clearfix">
					<span class="bPrice"><strong>8 592</strong> <span class="rubl">p</span></span>
					<span class="bOptions"><span class="bDecor">Варианты</span></span>
				</div>

				<div class="bOptionsSection">
					<i class="bCorner"></i>
					<i class="bCornerDark"></i>

					<ul class="bOptionsList">
						<li class="bOptionsList__eItem">Цвет обивки</li>
						<li class="bOptionsList__eItem">Наличие подъемного механизма</li>
					</ul>
				</div>

				<div class="bBtnLine clearfix">
					<div class="btnBuy"><a class="btnBuy__eLink" href="">Купить</a></div>
					<a class="btnView mBtnGrey" href="">Посмотреть</a>
				</div>
			</div>
		</li>

		<li class="bListingItem">
			<div class="bListingItem__eInner">
				<div class="bSimplyDesc">
					<p class="bSimplyDesc__eText"><a href="">Универсальный набор</a></p>
				</div>

				<a href="" class="bProductImg"><img class="bProductImg__eImg" src="http://fs03.enter.ru/1/1/500/f3/207661.jpg" /></a>

				<div class="bPriceLine clearfix">
					<span class="bAvailible">Только в магазинах</span>
				</div>

				<div class="bPriceLine clearfix">
					<span class="bPrice"><strong>8 592</strong> <span class="rubl">p</span></span>
					<span class="bOptions"><span class="bDecor">Варианты</span></span>
				</div>

				<div class="bOptionsSection">
					<i class="bCorner"></i>
					<i class="bCornerDark"></i>

					<ul class="bOptionsList">
						<li class="bOptionsList__eItem">Цвет обивки</li>
						<li class="bOptionsList__eItem">Наличие подъемного механизма</li>
					</ul>
				</div>

				<div class="bBtnLine clearfix">
					<div class="btnBuy"><a class="btnBuy__eLink" href="">Купить</a></div>
					<a class="btnView mBtnGrey" href="">Посмотреть</a>
				</div>
			</div>
		</li>

		<li class="bListingItem">
			<div class="bListingItem__eInner">
				<div class="bSimplyDesc">
					<p class="bSimplyDesc__eText"><a href="">Универсальный набор (инструментальный ящик) Jonnesway C-3DH201 торцевых головок 1/2" DR, 10-32 мм, ключей 6-22 мм</a></p>
					<ul class="bSimplyDescStikers">
						<li class="bSimplyDescStikers__eItem mLeftStiker"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/stiker.png" /></li>

						<li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/video.png" /></li>
						<li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/grad360.png" /></li>
					</ul>
				</div>

				<a href="" class="bProductImg"><img class="bProductImg__eImg" src="http://fs03.enter.ru/1/1/500/f3/207661.jpg" /></a>

				<div class="bPriceLine clearfix">
					<span class="bPriceOld"><strong class="bDecor">8 592</strong> <span class="rubl">p</span></span>
				</div>

				<div class="bPriceLine clearfix">
					<span class="bPrice"><strong>8 592</strong> <span class="rubl">p</span></span>
				</div>

				<div class="bBtnLine clearfix">
					<div class="btnBuy"><a class="btnBuy__eLink" href="">Купить</a></div>
					<a class="btnView mBtnGrey" href="">Посмотреть</a>
				</div>
			</div>
		</li>

		<li class="bListingItem mLast">
			<div class="bListingItem__eInner">
				<div class="bSimplyDesc">
					<p class="bSimplyDesc__eText"><a href="">Универсальный набор (инструментальный ящик) Jonnesway C-3DH201</a></p>
					<ul class="bSimplyDescStikers">
						<li class="bSimplyDescStikers__eItem mLeftStiker"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/stiker.png" /></li>

						<li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/video.png" /></li>
						<li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/grad360.png" /></li>
					</ul>
				</div>

				<a href="" class="bProductImg"><img class="bProductImg__eImg" src="http://fs03.enter.ru/1/1/500/f3/207661.jpg" /></a>

				<div class="bPriceLine clearfix">
					<span class="bPriceOld"><strong class="bDecor">8 592</strong> <span class="rubl">p</span></span>
				</div>

				<div class="bPriceLine clearfix">
					<span class="bPrice"><strong>8 592</strong> <span class="rubl">p</span></span>
				</div>

				<div class="bBtnLine clearfix">
					<div class="btnBuy"><a class="btnBuy__eLink" href="">Купить</a></div>
					<a class="btnView mBtnGrey" href="">Посмотреть</a>
				</div>
			</div>
		</li>

		<li class="bListingItem">
			<div class="bListingItem__eInner">
				<div class="bSimplyDesc">
					<p class="bSimplyDesc__eText"><a href="">Универсальный набор (инструментальный ящик) Jonnesway C-3DH201 торцевых головок 1/2" DR, 10-32 мм, ключей 6-22 мм</a></p>
					<ul class="bSimplyDescStikers">
						<li class="bSimplyDescStikers__eItem mLeftStiker"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/stiker.png" /></li>

						<li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/video.png" /></li>
						<li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/grad360.png" /></li>
					</ul>
				</div>

				<a href="" class="bProductImg"><img class="bProductImg__eImg" src="http://fs03.enter.ru/1/1/500/f3/207661.jpg" /></a>

				<div class="bPriceLine clearfix">
					<span class="bPriceOld"><strong class="bDecor">8 592</strong> <span class="rubl">p</span></span>
					<span class="bAvailible">Только в магазинах</span>
				</div>

				<div class="bPriceLine clearfix">
					<span class="bPrice"><strong>8 592</strong> <span class="rubl">p</span></span>
					<span class="bOptions"><span class="bDecor">Варианты</span></span>
				</div>

				<div class="bOptionsSection">
					<i class="bCorner"></i>
					<i class="bCornerDark"></i>

					<ul class="bOptionsList">
						<li class="bOptionsList__eItem">Цвет обивки</li>
						<li class="bOptionsList__eItem">Наличие подъемного механизма</li>
					</ul>
				</div>

				<div class="bBtnLine clearfix">
					<div class="btnBuy"><a class="btnBuy__eLink" href="">Купить</a></div>
					<a class="btnView mBtnGrey" href="">Посмотреть</a>
				</div>
			</div>
		</li>
	</ul>
	<!-- /Листинг товаров -->

	<div class="bSortingLine mPagerBottom clearfix">
		<ul class="bSortingList">
			<li class="bSortingList__eItem mTitle">Страницы</li>

			<li class="bSortingList__eItem mSortItem mActive"><a class="bSortingList__eLink" href="">1</a></li>
			<li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink" href="">2</a></li>
			<li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink" href="">3</a></li>
			<li class="bSortingList__eItem mSortItem mDotted">&#8230;</li>
			<li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink" href="">48</a></li>
			<li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink mMore" href="">&#8734;</a></li>
		</ul>
	</div>
</div>