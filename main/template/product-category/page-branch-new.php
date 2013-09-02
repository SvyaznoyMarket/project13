<?php
/**
 * @var $page                    \View\ProductCategory\BranchPage
 * @var $category                \Model\Product\Category\Entity
 * @var $productFilter           \Model\Product\Filter
 * @var $productPagersByCategory \Iterator\EntityPager[]
 * @var $productVideosByProduct  array
 */
?>

<div class="bCatalog">

	<!-- Хлебные крохи -->
	<ul class="bBreadcrumbs clearfix">
		<li class="bBreadcrumbs__eItem"><a class="bBreadcrumbs__eLink" href="">Товары на каждый день</a></li>
		<li class="bBreadcrumbs__eItem mLast"><a class="bBreadcrumbs__eLink" href="">Товары для дома</a></li>
	</ul>
	<!-- /Хлебные крохи -->

	<h1  class="bTitlePage">Товары для дома</h1>

	<!-- Категории товаров -->
	<ul class="bCatalogList clearfix">
		<li class="bCatalogList__eItem">
			<a class="bCatalogList__eLink" href="">
				<span class="bCategoriesImg">
					<img class="bCategoriesImg__eImg" src="http://fs10.enter.ru/6/1/163/70/214936.jpg" alt="" />
				</span>

				<span class="bCategoriesName">Освежители воздуха</span>
			</a>
		</li>

		<li class="bCatalogList__eItem">
			<a class="bCatalogList__eLink" href="">
				<span class="bCategoriesImg">
					<img class="bCategoriesImg__eImg" src="http://fs10.enter.ru/6/1/163/70/214936.jpg" alt="" />
				</span>

				<span class="bCategoriesName">Освежители воздуха</span>
			</a>
		</li>

		<li class="bCatalogList__eItem mLast">
			<a class="bCatalogList__eLink" href="">
				<span class="bCategoriesImg">
					<img class="bCategoriesImg__eImg" src="http://fs10.enter.ru/6/1/163/70/214936.jpg" alt="" />
				</span>

				<span class="bCategoriesName">Освежители воздуха</span>
			</a>
		</li>

		<li class="bCatalogList__eItem">
			<a class="bCatalogList__eLink" href="">
				<span class="bCategoriesImg">
					<img class="bCategoriesImg__eImg" src="http://fs10.enter.ru/6/1/163/70/214936.jpg" alt="" />
				</span>

				<span class="bCategoriesName">Млющие средства для посуды и освежители воздуха</span>
			</a>
		</li>

		<li class="bCatalogList__eItem">
			<a class="bCatalogList__eLink" href="">
				<span class="bCategoriesImg">
					<img class="bCategoriesImg__eImg" src="http://fs10.enter.ru/6/1/163/70/214936.jpg" alt="" />
				</span>

				<span class="bCategoriesName">Освежители воздуха</span>
			</a>
		</li>

		<li class="bCatalogList__eItem mLast">
			<a class="bCatalogList__eLink" href="">
				<span class="bCategoriesImg">
					<img class="bCategoriesImg__eImg" src="http://fs10.enter.ru/6/1/163/70/214936.jpg" alt="" />
				</span>

				<span class="bCategoriesName">Освежители воздуха</span>
			</a>
		</li>

		<li class="bCatalogList__eItem">
			<a class="bCatalogList__eLink" href="">
				<span class="bCategoriesImg">
					<img class="bCategoriesImg__eImg" src="http://fs10.enter.ru/6/1/163/70/214936.jpg" alt="" />
				</span>

				<span class="bCategoriesName">Освежители воздуха</span>
			</a>
		</li>
	</ul>
	<!-- /Категории товаров -->
 
 	<!-- Фильтр товаров -->
	<div class="bFilter clearfix">
		<div class="bFilterHead">
			<a class="bFilterToggle mClose" href=""><span class="bToggleText">Бренды и параметры</span></a>

			<!-- Фильтр по цене -->
			<div class="bFilterPrice">
				<span class="bFilterPrice__eTitle">Цена</span>
				<input class="bFilterPrice__eInput" name="" value="1 000" type="text"  />

	            <div class="bFilterSlider">
	            	<div class="ui-slider-range ui-widget-header ui-corner-all" style="left: 0%; width: 50%;"></div>
	            	<a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: -14px;"></a>
	            	<a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 50%;"></a>
	            </div>

	            <input class="bFilterPrice__eInput mLast" name="" value="10 000" type="text"  /> 

	            <span class="bFilterPrice__eRub rubl">p</span>
		    </div>
		    <!-- /Фильтр по цене -->

		    <!-- Фильтр по популярным позициям -->
			<ul class="bPopularSection">
				<li class="bPopularSection__eItem mTitle">Популярные бренды</li>
				<li class="bPopularSection__eItem"><strong class="bPopularSection__eText">Samsung</strong></li>
				<li class="bPopularSection__eItem"><strong class="bPopularSection__eText">Nokia</strong></li>
				<li class="bPopularSection__eItem"><strong class="bPopularSection__eText">Roga und Koppentenganger</strong></li>
				<li class="bPopularSection__eItem"><strong class="bPopularSection__eText">Dr. Buchman</strong></li>
				<li class="bPopularSection__eItem"><strong class="bPopularSection__eText"></strong></li>
			</ul>
			<!-- /Фильтр по популярным позициям -->
		</div>
	</div>
	<!-- Фильтр товаров -->

	<div class="bFilter clearfix">
		<div class="bFilterHead">
			<a class="bFilterToggle mOpen" href=""><span class="bToggleText">Бренды и параметры</span></a>

			<div class="bFilterPrice">
				<span class="bFilterPrice__eTitle">Цена</span>
				<input class="bFilterPrice__eInput" name="" value="1 000" type="text"  />

	            <div class="bFilterSlider">
	            	<div class="ui-slider-range ui-widget-header ui-corner-all" style="left: 0%; width: 50%;"></div>
	            	<a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: -14px;"></a>
	            	<a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 50%;"></a>
	            </div>

	            <input class="bFilterPrice__eInput mLast" name="" value="10 000" type="text"  /> 

	            <span class="bFilterPrice__eRub rubl">p</span>
		    </div>
		</div>

		<!-- Фильтр по выбранным параметрам -->
		<div class="bFilterCont clearfix">
			<!-- Список названий параметров -->
			<ul class="bFilterParams">
				<li class="bFilterParams__eItem mActive"><span class="bParamName">Бренд</span></li>
				<li class="bFilterParams__eItem"><span class="bParamName">Тип</span></li>
				<li class="bFilterParams__eItem"><span class="bParamName">Для кого</span></li>
				<li class="bFilterParams__eItem"><span class="bParamName">Объем</span></li>
				<li class="bFilterParams__eItem"><span class="bParamName">Для какой кожи по возрасту</span></li>
				<li class="bFilterParams__eItem"><span class="bParamName">Страна-производитель</span></li>
				<li class="bFilterParams__eItem"><span class="bParamName">Периодичность использования</span></li>
			</ul>
			<!-- /Список названий параметров -->

			<!-- Список значений параметров -->
			<div class="bFilterValues">
				<div class="bFilterValuesItem clearfix">

					<div class="bFilterValuesCol">
						<input class="bInputHidden" type="checkbox" id="check1" name="" hidden />
						<label class="bFilterCheckbox" for="check1">
							Ahava
						</label>
					</div>

					<div class="bFilterValuesCol">
						<input class="bInputHidden" type="checkbox" id="check2" name="" hidden />
						<label class="bFilterCheckbox" for="check2">
							Ahava
						</label>
					</div>

					<div class="bFilterValuesCol">
						<input class="bInputHidden" type="checkbox" id="check3" name="" hidden />
						<label class="bFilterCheckbox" for="check3">
							Ahava
						</label>
					</div>

					<div class="bFilterValuesCol">
						<input class="bInputHidden" type="checkbox" id="check4" name="" hidden />
						<label class="bFilterCheckbox" for="check4">
							Ahava
						</label>
					</div>

					<div class="bFilterValuesCol">
						<input class="bInputHidden" type="checkbox" id="check5" name="" hidden />
						<label class="bFilterCheckbox" for="check5">
							Ahava
						</label>
					</div>

					<div class="bFilterValuesCol">
						<input class="bInputHidden" type="checkbox" id="check6" name="" hidden />
						<label class="bFilterCheckbox" for="check6">
							Ahava
						</label>
					</div>

					<div class="bFilterValuesCol">
						<input class="bInputHidden" type="checkbox" id="check7" name="" hidden />
						<label class="bFilterCheckbox" for="check7">
							Ahava
						</label>
					</div>

					<div class="bFilterValuesCol">
						<input class="bInputHidden" type="checkbox" id="check8" name="" hidden />
						<label class="bFilterCheckbox" for="check8">
							Ahava
						</label>
					</div>

					<div class="bFilterValuesCol">
						<input class="bInputHidden" type="checkbox" id="check9" name="" hidden />
						<label class="bFilterCheckbox" for="check9">
							Ahava
						</label>
					</div>

					<div class="bFilterValuesCol">
						<input class="bInputHidden" type="checkbox" id="check10" name="" hidden />
						<label class="bFilterCheckbox" for="check10">
							Ahava
						</label>
					</div>

					<div class="bFilterValuesCol">
						<input class="bInputHidden" type="checkbox" id="check11" name="" hidden />
						<label class="bFilterCheckbox" for="check11">
							Ahava
						</label>
					</div>
				</div>

				<div class="bBtnPick clearfix"><a class="bBtnPick__eLink mBtnGrey" href="">Подобрать</a></div>
			</div>
			<!-- /Список значений параметров -->
		</div>
		<!-- /Фильтр по выбранным параметрам -->

		<!-- Списоки выбранных параметров -->
		<div class="bFilterFoot">
			<ul class="bFilterCheckedParams clearfix">
				<li class="bFilterCheckedParams__eItem mTitle">Цена</li>

				<li class="bFilterCheckedParams__eItem mParams"><a class="bDelete" href=""></a><span class="bParamsName">от 2 000p</span></li>

				<li class="bFilterCheckedParams__eItem mParams"><a class="bDelete" href=""></a><span class="bParamsName">до 1 000 000p</span></li>
			</ul>

			<ul class="bFilterCheckedParams clearfix mLast">
				<li class="bFilterCheckedParams__eItem mTitle">Бренд</li>

				<li class="bFilterCheckedParams__eItem mParams"><a class="bDelete" href=""></a><span class="bParamsName">Ahava</span></li>

				<li class="bFilterCheckedParams__eItem mParams"><a class="bDelete" href=""></a><span class="bParamsName">Bubchen</span></li>

				<li class="bFilterCheckedParams__eItem mParams"><a class="bDelete" href=""></a><span class="bParamsName">Агентство старинных развлечений "Работорцы"</span></li>

				<li class="bFilterCheckedParams__eItem mParams mClearAll"><a class="bDelete" href=""><strong class="bParamsName">Очистить все</strong></a></li> <!-- Добаялется только в списке идущем по очереди последним -->
			</ul>
		</div>
		<!-- /Списоки выбранных параметров -->
	</div>

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