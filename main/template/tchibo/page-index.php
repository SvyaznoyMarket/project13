<?php
/**
 * @var $page         \View\DefaultLayout
 * @var $gridCells    \Model\GridCell\Entity[]
 * @var $productsById \Model\Product\CompactEntity[]
 */
?>

<?
$helper = new \Helper\TemplateHelper();
?>

<h1 class="tchiboTitle">Мужчины</h1>

<div class="tchiboNavSection">
	<img class="tchiboNavSection__img" src="/styles/tchiboNavSection/img/menuSecImg.jpg" />

	<h2 class="tchiboNavSection__title">Мечта мужчин</h2>

	<ul class="tchiboNavSection__list">
		<li class="item">
			<a class="link mActive" href="">Мечта мужчин</a>

			<div class="itemHover">
				<img class="itemHover__img" src="/styles/tchiboNavSection/img/hoverImg.jpg" />
				<div class="itemHover__border"></div>
			</div>
		</li>

		<li class="item">
			<a class="link" href="">Классика</a>

			<div class="itemHover">
				<img class="itemHover__img" src="/styles/tchiboNavSection/img/hoverImg.jpg" />
				<div class="itemHover__border"></div>
			</div>
		</li>

		<li class="item">
			<a class="link" href="">Мужской сезон</a>

			<div class="itemHover">
				<img class="itemHover__img" src="/styles/tchiboNavSection/img/hoverImg.jpg" />
				<div class="itemHover__border"></div>
			</div>
		</li>

		<li class="item">
			<a class="link" href="">Коллекция с длинным-предлинным названием</a>

			<div class="itemHover">
				<img class="itemHover__img" src="/styles/tchiboNavSection/img/hoverImg.jpg" />
				<div class="itemHover__border"></div>
			</div>
		</li>

		<li class="item">
			<a class="link" href="">100% натуральное</a>

			<div class="itemHover">
				<img class="itemHover__img" src="/styles/tchiboNavSection/img/hoverImg.jpg" />
				<div class="itemHover__border"></div>
			</div>
		</li>

		<li class="item">
			<a class="link" href="">Ещё коллекция</a>

			<div class="itemHover">
				<img class="itemHover__img" src="/styles/tchiboNavSection/img/hoverImg.jpg" />
				<div class="itemHover__border"></div>
			</div>
		</li>

		<li class="item">
			<a class="link" href="">Какая-то ещё коллекция</a>

			<div class="itemHover">
				<img class="itemHover__img" src="/styles/tchiboNavSection/img/hoverImg.jpg" />
				<div class="itemHover__border"></div>
			</div>
		</li>

		<li class="item">
			<a class="link" href="">100% натуральное</a>

			<div class="itemHover">
				<img class="itemHover__img" src="/styles/tchiboNavSection/img/hoverImg.jpg" />
				<div class="itemHover__border"></div>
			</div>
		</li>
	</ul>
</div>


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
		</ul>
	</div>
</div>

<div class="tchiboNavSliderWrap">
	<div class="tchiboNavSlider">
		<div class="tchiboNavSlider__title"></div>

		<div class="tchiboNavSlider__inner">
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
		</div>
	</div>
</div>

<div class="bPromoCatalogSlider mTchiboSlider">
    <a href="#" class="bPromoCatalogSlider_eArrow mArLeft"></a>
    <a href="#" class="bPromoCatalogSlider_eArrow mArRight"></a>
    <div class="bPromoCatalogSliderWrap clearfix" style="width: 7710px;">
    	<div class="bPromoCatalogSliderWrap_eSlide">
    		<a class="bPromoCatalogSliderWrap_eSlideLink topLifted" href="">
    			<div class="bottomLifted">
    				<img src="/styles/tchibo/img/Img.png" alt="">
    				<ul class="bCatLine">
    					<li class="bCatLine__eItem">Дом</li>
    					<li class="bCatLine__eItem">Дизайн для ванной и души</li>
    				</ul>
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

<?
$contentHeight = 0;
foreach ($gridCells as $cell) {
    $height =
        (($cell->getRow() - 1) *  60 + ($cell->getRow() - 1) * 20)
        + ($cell->getSizeY() * 60 + ($cell->getSizeY() - 1) * 20);
    if ($height > $contentHeight) {
        $contentHeight = $height;
    }
}
?>
<div class="tchiboProducts" style="position: relative; height: <?= $contentHeight ?>px; margin: 30px 0;">
<?= $helper->render('grid/__show', [
    'gridCells'    => $gridCells,
    'productsById' => $productsById,
]) ?>
</div>