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

	<ul class="bBreadcrumbs clearfix">
		<li class="bBreadcrumbs__eItem"><a class="bBreadcrumbs__eLink" href="">Товары на каждый день</a></li>
		<li class="bBreadcrumbs__eItem mLast"><a class="bBreadcrumbs__eLink" href="">Товары для дома</a></li>
	</ul>

	<h1  class="bTitlePage">Товары для дома</h1>

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
 
	<div class="bFilter clearfix">
		<div class="bFilterHead">
			<a class="bFilterToggle mClose" href=""><span class="bToggleText">Бренды и параметры</span></a>

			<div class="bFilterPrice">
			</div>

			<ul class="bPopularSection">
				<li class="bPopularSection__eItem mTitle">Популярные бренды</li>
				<li class="bPopularSection__eItem"><strong class="bPopularSection__eText">Samsung</strong></li>
				<li class="bPopularSection__eItem"><strong class="bPopularSection__eText">Nokia</strong></li>
				<li class="bPopularSection__eItem"><strong class="bPopularSection__eText">Roga und Koppentenganger</strong></li>
				<li class="bPopularSection__eItem"><strong class="bPopularSection__eText">Dr. Buchman</strong></li>
				<li class="bPopularSection__eItem"><strong class="bPopularSection__eText"></strong></li>
			</ul>
		</div>
	</div>

	<div class="bFilter clearfix">
		<div class="bFilterHead">
			<a class="bFilterToggle mOpen" href=""><span class="bToggleText">Бренды и параметры</span></a>

			<div class="bFilterPrice">
			</div>
		</div>

		<div class="bFilterCont">
			<ul class="bFilterParams">
				<li class="bFilterParams__eItem mActive"><span class="bParamName">Бренд</span></li>
				<li class="bFilterParams__eItem"><span class="bParamName">Тип</span></li>
				<li class="bFilterParams__eItem"><span class="bParamName">Для кого</span></li>
				<li class="bFilterParams__eItem"><span class="bParamName">Объем</span></li>
				<li class="bFilterParams__eItem"><span class="bParamName">Для какой кожи по возрасту</span></li>
				<li class="bFilterParams__eItem"><span class="bParamName">Страна-производитель</span></li>
				<li class="bFilterParams__eItem"><span class="bParamName">Периодичность использования</span></li>
			</ul>

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
				</div>

				<div class="bBtnPick clearfix"><a class="bBtnPick__eLink mBtnGrey" href="">Подобрать</a></div>
			</div>
		</div>

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
	</div>

	<div class="bSortingLine clearfix">
		<ul class="bSortingList mSorting">
			<li class="bSortingList__eItem mTitle">Сортировать</li>

			<li class="bSortingList__eItem mSortItem mActive"><a class="bSortingList__eLink" href="">Автоматически</a></li>
			<li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink" href="">Лидеры продаж</a></li>
			<li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink" href="">Новинки</a></li>
			<li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink" href="">Сначала недорогие</a></li>
			<li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink" href="">Сначала дорогие</a></li>
		</ul>

		<ul class="bSortingList mViewer">
			<li class="bSortingList__eItem mTitle">Вид</li>

			<li class="bSortingList__eItem mSortItem mActive"><a class="bSortingList__eLink mTable" href=""><span class="bIco mIcoTable"></span></a></li>
			<li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink mLine" href=""><span class="bIco mIcoLine"></span></a></li>
		</ul>

		<ul class="bSortingList mPager">
			<li class="bSortingList__eItem mTitle">Страницы</li>

			<li class="bSortingList__eItem mSortItem mActive"><a class="bSortingList__eLink" href="">123</a></li>
			<li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink" href="">&#8734;</a></li>
		</ul>
	</div>

	<script>
		$(document).ready(function(){
		    $(".bListingItem").hover(
		    function(){
		        $(this).children(".bListingItem__eInner").css('z-index', '10');
		    },

		    function(){
		        $(this).children(".bListingItem__eInner").css('z-index', '5');
		    }
		    );
		});
	</script>


	<ul class="bListing clearfix">
		<li class="bListingItem">
			<div class="bListingItem__eInner">
				<div class="bSimplyDesc">
					<p class="bSimplyDesc__eText">Универсальный набор (инструментальный ящик) Jonnesway C-3DH201 торцевых головок 1/2" DR, 10-32 мм, ключей 6-22 мм, угловых ключей 1,5-10 мм, отверток, 66 предметов</p>
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
					<a class="btnView" href="">Посмотреть</a>
				</div>
			</div>
		</li>

		<li class="bListingItem">
			<div class="bListingItem__eInner">
				<div class="bSimplyDesc">
					<p class="bSimplyDesc__eText">Универсальный набор (инструментальный ящик) Jonnesway C-3DH201 торцевых головок 1/2" DR, 10-32 мм</p>
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
					<a class="btnView" href="">Посмотреть</a>
				</div>
			</div>
		</li>

		<li class="bListingItem">
			<div class="bListingItem__eInner">
				<div class="bSimplyDesc">
					<p class="bSimplyDesc__eText">Универсальный набор (инструментальный ящик) Jonnesway C-3DH201 торцевых головок 1/2" DR, 10-32 мм, ключей 6-22 мм, угловых ключей 1,5-10 мм, отверток, 66 предметов</p>
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
					<a class="btnView" href="">Посмотреть</a>
				</div>
			</div>
		</li>

		<li class="bListingItem mLast">
			<div class="bListingItem__eInner">
				<div class="bSimplyDesc">
					<p class="bSimplyDesc__eText">Универсальный набор (инструментальный ящик) Jonnesway C-3DH201 торцевых головок 1/2" DR, 10-32 мм, ключей 6-22 мм, угловых ключей 1,5-10 мм, отверток, 66 предметов</p>
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
					<a class="btnView" href="">Посмотреть</a>
				</div>
			</div>
		</li>

		<li class="bListingItem">
			<div class="bListingItem__eInner">
				<div class="bSimplyDesc">
					<p class="bSimplyDesc__eText">Универсальный набор (инструментальный ящик) Jonnesway C-3DH201 торцевых головок</p>
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
					<a class="btnView" href="">Посмотреть</a>
				</div>
			</div>
		</li>

		<li class="bListingItem">
			<div class="bListingItem__eInner">
				<div class="bSimplyDesc">
					<p class="bSimplyDesc__eText">Универсальный набор (инструментальный ящик) Jonnesway C-3DH201 торцевых головок 1/2" DR, 10-32 мм, ключей 6-22 мм, угловых ключей 1,5-10 мм, отверток, 66 предметов</p>
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
					<a class="btnView" href="">Посмотреть</a>
				</div>
			</div>
		</li>

		<li class="bListingItem">
			<div class="bListingItem__eInner">
				<div class="bSimplyDesc">
					<p class="bSimplyDesc__eText">Универсальный набор (инструментальный ящик) Jonnesway C-3DH201 торцевых головок 1/2" DR, 10-32 мм, ключей 6-22 мм, угловых ключей 1,5-10 мм, отверток, 66 предметов</p>
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
					<a class="btnView" href="">Посмотреть</a>
				</div>
			</div>
		</li>

		<li class="bListingItem mLast">
			<div class="bListingItem__eInner">
				<div class="bSimplyDesc">
					<p class="bSimplyDesc__eText">Универсальный набор (инструментальный ящик) Jonnesway C-3DH201 торцевых головок 1/2" DR, 10-32 мм, ключей 6-22 мм, угловых ключей 1,5-10 мм, отверток, 66 предметов</p>
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
					<a class="btnView" href="">Посмотреть</a>
				</div>
			</div>
		</li>

		<li class="bListingItem">
			<div class="bListingItem__eInner">
				<div class="bSimplyDesc">
					<p class="bSimplyDesc__eText">Универсальный набор (инструментальный ящик) Jonnesway C-3DH201 торцевых головок 1/2" DR, 10-32 мм, ключей 6-22 мм, угловых ключей 1,5-10 мм, отверток, 66 предметов</p>
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
					<a class="btnView" href="">Посмотреть</a>
				</div>
			</div>
		</li>
	</ul>

	<div class="bSortingLine clearfix">
		<ul class="bSortingList mPagerBottom">
			<li class="bSortingList__eItem mTitle">Страницы</li>

			<li class="bSortingList__eItem mSortItem mActive"><a class="bSortingList__eLink" href="">1</a></li>
			<li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink" href="">2</a></li>
			<li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink" href="">3</a></li>
			<li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink" href="">4</a></li>
			<li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink" href="">8</a></li>
			<li class="bSortingList__eItem mSortItem">&#8230;</li>
			<li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink" href="">&#8734;</a></li>
		</ul>
	</div>
</div>