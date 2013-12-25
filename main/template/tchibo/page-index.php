<?php
/**
 * @var $page         \View\DefaultLayout
 */
?>
<!-- TCHIBO - слайдер-меню разделов Чибо -->
<div class="tchiboNavSliderWrap">
	<div class="tchiboNavSlider">
		<div class="tchiboNavSlider__title"></div>

		<div class="tchiboNavSlider__inner">
			<div class="tdRelative">
				<ul class="tchiboNavSlider__list">
					<li class="item">
						<a class="link" href="">
							<img class="img" src="/styles/tchibo/img/ImgCat.png" />
							<span class="title">Мужчины</span>
						</a>
					</li>

					<li class="item">
						<a class="link" href="">
							<img class="img" src="/styles/tchibo/img/ImgCat.png" />
							<span class="title">Женщины</span>
						</a>
					</li>

					<li class="item">
						<a class="link" href="">
							<img class="img" src="/styles/tchibo/img/ImgCat.png" />
							<span class="title">Дети</span>
						</a>
					</li>

					<li class="item">
						<a class="link" href="">
							<img class="img" src="/styles/tchibo/img/ImgCat.png" />
							<span class="title">Дом</span>
						</a>
					</li>

					<li class="item">
						<a class="link" href="">
							<img class="img" src="/styles/tchibo/img/ImgCat.png" />
							<span class="title">Спорт</span>
						</a>
					</li>

					<li class="item">
						<a class="link" href="">
							<img class="img" src="/styles/tchibo/img/ImgCat.png" />
							<span class="title">Кофе</span>
						</a>
					</li>

					<li class="item">
						<a class="link" href="">
							<img class="img" src="/styles/tchibo/img/ImgCat.png" />
							<span class="title">Мужчины</span>
						</a>
					</li>

					<li class="item">
						<a class="link" href="">
							<img class="img" src="/styles/tchibo/img/ImgCat.png" />
							<span class="title">Женщины</span>
						</a>
					</li>

					<li class="item">
						<a class="link" href="">
							<img class="img" src="/styles/tchibo/img/ImgCat.png" />
							<span class="title">Дети</span>
						</a>
					</li>
				</ul>
				<div class="sliderBtn mLeftBtn"><a class="sliderBtn__link" href=""></a></div><!--/ кнопка прокрутки влево -->
			</div>
		</div>
		<div class="sliderBtn mRightBtn"><a class="sliderBtn__link" href=""></a></div><!--/ кнопка прокрутки влево -->
	</div>
</div>
<!--/ TCHIBO - слайдер-меню разделов Чибо -->

<!-- TCHIBO - крутилка разделов Чибо на рутовой странице -->
<div class="bPromoCatalogSlider mTchiboSlider">
    <a href="#" class="bPromoCatalogSlider_eArrow mArLeft"></a>
    <a href="#" class="bPromoCatalogSlider_eArrow mArRight"></a>
    <div class="bPromoCatalogSliderWrap clearfix" style="width: 7710px;">
    	<div class="bPromoCatalogSliderWrap_eSlide">
    		<a class="bPromoCatalogSliderWrap_eSlideLink topLifted" href="">
    			<div class="bottomLifted">
    				<img src="/styles/tchibo/img/Img.png" alt="">
    				<div class="bCatLine">
    					Какая-то заманивающая надпись
    				</div>
    			</div>
    		</a>
    	</div>
    	<div class="bPromoCatalogSliderWrap_eSlide">
    	 	<a class="bPromoCatalogSliderWrap_eSlideLink topLifted" href="">
    	 		<div class="bottomLifted"><img src="/styles/tchibo/img/Img.png" alt=""></div>
    	 	</a>
    	</div>
    	<div class="bPromoCatalogSliderWrap_eSlide">
    	 	<a class="bPromoCatalogSliderWrap_eSlideLink topLifted" href="">
    	 		<div class="bottomLifted"><img src="/styles/tchibo/img/Img.png" alt=""></div>
    	 	</a>
    	</div>
    	<div class="bPromoCatalogSliderWrap_eSlide">
    	 	<a class="bPromoCatalogSliderWrap_eSlideLink topLifted" href="">
			    <div class="bottomLifted"><img src="/styles/tchibo/img/Img.png" alt=""></div>
			</a>
		</div>
	</div>
</div>

<div id="promoCatalogPaginator" class="bPaginator mTchiboPaginator clearfix">
	<div class="bPaginator_eWrap clearfix">
		<a class="bPaginator_eLink" href="">1</a>
		<a class="bPaginator_eLink" href="">2</a>
		<a class="bPaginator_eLink active" href="">3</a>
		<a class="bPaginator_eLink" href="">4</a>
	</div>
</div>
<!--/ TCHIBO - крутилка разделов Чибо на рутовой странице -->

<!--TCHIBO - каталог разделов, баннеров, товаров Чибо -->
<div class="tchiboCatalog">	
	<div class="tchiboCatalogInner">
		<a href=""><img class="tchiboCatalog__img" src="/styles/tchiboCatalog/img/tchiboCatalog.jpg" /></a>

		<a href="" class="tchiboCatalog__title">Мужчины</a>

		<ul class="tchiboCatalog__list">
			<li class="item"><a class="link" href="">Мечта мужчин</a></li>
			<li class="item"><a class="link" href="">Какая-то ещё коллекция</a></li>
			<li class="item"><a class="link" href="">Коллекция с длинным-предлинным названием</a></li>
			<li class="item"><a class="link" href="">100% натуральное</a></li>
			<li class="item"><a class="link" href="">Ещё коллекция</a></li>
			<li class="item"><a class="link" href="">Мужской сезон</a></li>
		</ul><!--/ список подкатегории -->
	</div><!--/ категория -->

	<div class="tchiboCatalogInner mBanner">
		<a href=""><img class="tchiboCatalog__img" src="/styles/tchiboCatalog/img/tchiboCatalog.jpg" /></a>

		<a href="" class="tchiboCatalog__title">Баннер</a>
	</div> <!--/ вывод баннера или категории без списка подкатегорий и верхней плашкой-заголовком -->
</div>
<!--/ TCHIBO - каталог разделов, баннеров, товаров Чибо -->