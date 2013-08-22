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