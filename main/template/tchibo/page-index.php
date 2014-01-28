<?php
/**
 * @var $page         \View\DefaultLayout
 */
?>
<? /*<div class="tchiboNavSliderWrap">
	<div class="tchiboNavSlider">
		<div class="tchiboNavSlider__title"></div>

		<div class="tchiboNavSlider__inner">
			<div class="tdRelative">
				<ul class="tchiboNavSlider__list">
					<li class="item">
						<a class="link" href="">
							<img class="img" src="/styles/tchiboCatalog/img/manMini.jpg" />
							<span class="title">Мужчины</span>
						</a>
					</li>

					<li class="item">
						<a class="link" href="">
							<img class="img" src="/styles/tchiboCatalog/img/womanMini.jpg" />
							<span class="title">Женщины</span>
						</a>
					</li>

					<li class="item">
						<a class="link" href="">
							<img class="img" src="/styles/tchiboCatalog/img/kidsMini.jpg" />
							<span class="title">Дети</span>
						</a>
					</li>

					<li class="item">
						<a class="link" href="">
							<img class="img" src="/styles/tchiboCatalog/img/homeMini.jpg" />
							<span class="title">Дом</span>
						</a>
					</li>

					<li class="item">
						<a class="link" href="">
							<img class="img" src="/styles/tchiboCatalog/img/sportMini.jpg" />
							<span class="title">Спорт</span>
						</a>
					</li>

					<li class="item">
						<a class="link" href="">
							<img class="img" src="/styles/tchiboCatalog/img/cofeeMini.jpg" />
							<span class="title">Кофе</span>
						</a>
					</li>
				</ul>
				<!--div class="sliderBtn mLeftBtn"><a class="sliderBtn__link" href=""></a></div--><!--/ кнопка прокрутки влево -->
			</div>
		</div>
		<!--div class="sliderBtn mRightBtn"><a class="sliderBtn__link" href=""></a></div--><!--/ кнопка прокрутки влево -->
	</div>
</div>
*/ ?>

<!-- TCHIBO - крутилка разделов Чибо на рутовой странице -->

<div class="tchiboNav">
    <div class="tchiboNav__title"></div>

    <ul class="tchiboNav__list">
        <li class="item jsItemListTchibo">
	        <a class="link" href="/catalog/tchibo/genshchini-miss-sovershenstvo-38de">
	            Мисс Совершенство
	        </a>

	        <ul class="tchiboNav__sublist">
                <li class="sublistItem jsItemListTchibo"><a class="link" href="">Бугага</a></li>
                <li class="sublistItem jsItemListTchibo"><a class="link" href="">Бугагашенька</a></li>
            </ul>
    	</li>
        <li class="item jsItemListTchibo">
   			<a class="link" href="/catalog/tchibo/genshchini-elegantnoe-bele-dc04">
        		Элегантное белье
    		</a>
        </li>
    </ul>
</div>

<div id="promoCatalog" class="bPromoCatalog" data-slides="<?= $page->json($slideData) ?>">

	<script type="text/html" id="slide_tmpl">
	    <div class="bPromoCatalogSliderWrap_eSlide">
	        <a class="bPromoCatalogSliderWrap_eSlideLink topLifted" href="<%=linkUrl%>">
	            <div class="bottomLifted"><img src="<%=imgUrl%>" alt="<%=title%>"/></div>
	        </a>
	    </div>
	</script>

	<div class="bPromoCatalogSlider mTchiboSlider ">
	    <a href="#" class="bPromoCatalogSlider_eArrow mArLeft"></a>
	    <a href="#" class="bPromoCatalogSlider_eArrow mArRight"></a>
	    <div class="bPromoCatalogSliderWrap clearfix"></div>
	</div>

	<div id="promoCatalogPaginator" class="bPaginator mTchiboPaginator clearfix"></div>
</div>

<!--/ TCHIBO - крутилка разделов Чибо на рутовой странице -->

<!--TCHIBO - каталог разделов, баннеров, товаров Чибо -->
<div class="tchiboCatalog clearfix">	
	<div class="tchiboCatalogInner m312">
		<a href=""><img class="tchiboCatalog__img" src="/styles/tchiboCatalog/img/man.jpg" /></a>

		<a href="" class="tchiboCatalog__title">Мужчины</a>

		<? /*
		<ul class="tchiboCatalog__list">
			<li class="item"><a class="link" href="">Классика</a></li>
			<li class="item"><a class="link" href="">Мужской сезон</a></li>
			<li class="item"><a class="link" href="">Модные тренды</a></li>
			<li class="item"><a class="link" href="">100% натуральное</a></li>
			<li class="item"><a class="link" href="">Мечта мужчин</a></li>
			<li class="item"><a class="link" href="/catalog/tchibo/mugchini-za-rulem-33c7">За рулем</a></li>
		</ul><!--/ список подкатегории -->
		*/ ?>
	</div><!--/ категория -->

	<div class="tchiboCatalogInner m316">
		<a href=""><img class="tchiboCatalog__img" src="/styles/tchiboCatalog/img/woman.jpg" /></a>

		<a href="" class="tchiboCatalog__title">Женщины</a>

		<? /*
		<ul class="tchiboCatalog__list">
			<li class="item"><a class="link" href="">Классика</a></li>
			<li class="item"><a class="link" href="">Модные тренды</a></li>
			<li class="item"><a class="link" href="">100% натуральное</a></li>
			<li class="item"><a class="link" href="">За рулем</a></li>
			<li class="item"><a class="link" href="tchibo/genshchini-miss-sovershenstvo-38de">Мисс Совершенство</a></li>
			<li class="item"><a class="link" href="">Элегантное белье</a></li>
		</ul><!--/ список подкатегории -->
		*/ ?>
	</div><!--/ категория -->

	<div class="tchiboCatalogInner m312 mLast">
		<a href=""><img class="tchiboCatalog__img" src="/styles/tchiboCatalog/img/kids.jpg" /></a>

		<a href="" class="tchiboCatalog__title">Дети</a>
	</div><!--/ категория -->

	<div class="tchiboCatalogInner m470">
		<a href=""><img class="tchiboCatalog__img" src="/styles/tchiboCatalog/img/home.jpg" /></a>

		<a href="" class="tchiboCatalog__title">Дом</a>

		<? /*
		<ul class="tchiboCatalog__list">
			<li class="item"><a class="link" href="">Чистота дома</a></li>
			<li class="item"><a class="link" href="">Идеальный завтрак</a></li>
			<li class="item"><a class="link" href="">Уход за одеждой и шитье</a></li>
			<li class="item"><a class="link" href="">Дизайн для душа и души</a></li>
			<li class="item"><a class="link" href="/catalog/tchibo/dom-sdelay-sam-5d82">Сделай сам</a></li>
			<li class="item"><a class="link" href="">Кухня</a></li>
			<li class="item"><a class="link" href="">Компактное решение</a></li>
			<li class="item"><a class="link" href="">Теплая зима</a></li>
		</ul><!--/ список подкатегории -->
		*/ ?>
	</div><!--/ категория -->

	<div class="tchiboCatalogInner m470 mLast">
		<a href=""><img class="tchiboCatalog__img" src="/styles/tchiboCatalog/img/sport.jpg" /></a>

		<a href="" class="tchiboCatalog__title">Спорт</a>

		<? /*
		<ul class="tchiboCatalog__list">
			<li class="item"><a class="link" href="">Твои рекорды</a></li>
			<li class="item"><a class="link" href="">Активный отдых</a></li>
			<li class="item"><a class="link" href="">Фитнес</a></li>
		</ul><!--/ список подкатегории -->
		*/ ?>
	</div><!--/ категория -->

	<div class="tchiboCatalogInner">
		<a href=""><img class="tchiboCatalog__img" src="/styles/tchiboCatalog/img/cofee.jpg" /></a>

		<a href="" class="tchiboCatalog__title">Кофе</a>
	</div> <!--/ вывод баннера или категории без списка подкатегорий и верхней плашкой-заголовком -->
</div>
<!--/ TCHIBO - каталог разделов, баннеров, товаров Чибо -->