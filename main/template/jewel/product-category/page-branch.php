<?php
/**
 * @var $page                    \View\Jewel\ProductCategory\BranchPage
 * @var $category                \Model\Product\Category\Entity
 * @var $promoContent
 */
?>

<? if (\App::config()->adFox['enabled']): ?>
<div class="adfoxWrapper" id="adfox683sub"></div>
<? endif ?>

<div class="clear"></div>
<?= $page->tryRender('product-category/_categoryData', array('page' => $page, 'category' => $category)) ?>

<?= $promoContent ?>

<div class="logo-section">Ювелирные Украшения</div>

<nav class="brand-nav">
	<ul class="brand-nav__list clearfix">
		<li><a href="">Подвески-шармы</a></li>
		<li class="braslets"><a href="">Браслеты</a></li>
		<li><a href="">Кольца</a></li>
		<li><a href="">Серьги</a></li>
		<li class="kolye_kuloni"><a href="">Колье и<br/>кулоны</a></li>
		<li class="new"><a href="">Новинки</a></li>
	</ul>
</nav>

<div class="banner-categories">
	<img class="banner-categories__img" src="/css/pandoraCatalog/img/pandora_catalog_banner.jpg" alt="" />
	<div class="banner-categories__title">Подвески -<br/>шармы</div>
</div>

<nav class="brand-subnav clearfix">
	<div class="brand-subnav__title">Подвески - шармы</div>
	<ul class="brand-subnav__list">
		<li><a href="">Все</a></li>
		<li><a href="">Подвески-шармы</a></li>
		<li><a class="active" href="">Красивости</a></li>
		<li><a href="">Няшеньки</a></li>
		<li><a href="">Украшения</a></li>
		<li><a href="">Соединительные цепочки</a></li>
	</ul>
</nav>

<div class="filter-section">
	<ul class="filter-section__title clearfix">
		<li>Металлы</li>
		<li>Материалы</li>
		<li>Камни</li>
		<li>Темы</li>
		<li>Сортировать</li>
	</ul>

	<ul class="filter-section__value clearfix">
		<li><a href="">Все металлы</a>
			<ul class="filter-section__value__dd">
				<li><a href="">золото</a></li>
				<li><a href="">серебро</a></li>
				<li><a href="">бронза</a></li>
				<li><a href="">медь</a></li>
			</ul>
		</li>
		<li><a href="">Все материалы</a>
			<ul class="filter-section__value__dd">
				<li><a href="">золото</a></li>
				<li><a href="">серебро</a></li>
				<li><a href="">бронза</a></li>
				<li><a href="">медь</a></li>
			</ul>
		</li>
		<li><a href="">Все камни</a>
			<ul class="filter-section__value__dd">
				<li><a href="">золото</a></li>
				<li><a href="">серебро</a></li>
				<li><a href="">бронза</a></li>
				<li><a href="">медь</a></li>
			</ul>
		</li>
		<li><a href="">Все темы</a>
			<ul class="filter-section__value__dd">
				<li><a href="">золото</a></li>
				<li><a href="">серебро</a></li>
				<li><a href="">бронза</a></li>
				<li><a href="">медь</a></li>
			</ul>
		</li>
		<li><a href="">Как для себя</a>
			<ul class="filter-section__value__dd">
				<li><a href="">золото</a></li>
				<li><a href="">серебро</a></li>
				<li><a href="">бронза</a></li>
				<li><a href="">медь</a></li>
			</ul>
		</li>
	</ul>
</div>

<div class="items-section">
	<ul class="items-section__list">
		<li>
			<div class="item-name"><a href="">Название няши</a></div>
			<div class="item-img"><a href=""><img src="http://fs06.enter.ru/1/1/163/2d/58848.jpg" alt="" /></a></div>
			<div class="item-price">1,950 RUB</div>
			<div class="item-buy"><a href="">Купить</a></div>
		</li>

		<li>
			<div class="item-name"><a href="">Название няши</a></div>
			<div class="item-img"><a href=""><img src="http://fs06.enter.ru/1/1/163/2d/58848.jpg" alt="" /></a></div>
			<div class="item-price">1,950 RUB</div>
			<div class="item-buy"><a href="">Купить</a></div>
		</li>

		<li>
			<div class="item-name"><a href="">Название няши</a></div>
			<div class="item-img"><a href=""><img src="http://fs06.enter.ru/1/1/163/2d/58848.jpg" alt="" /></a></div>
			<div class="item-price">1,950 RUB</div>
			<div class="item-buy"><a href="">Купить</a></div>
		</li>

		<li>
			<div class="item-name"><a href="">Название няши</a></div>
			<div class="item-img"><a href=""><img src="http://fs06.enter.ru/1/1/163/2d/58848.jpg" alt="" /></a></div>
			<div class="item-price">1,950 RUB</div>
			<div class="item-buy"><a href="">Купить</a></div>
		</li>

		<li>
			<div class="item-name"><a href="">Название няши</a></div>
			<div class="item-img"><a href=""><img src="http://fs06.enter.ru/1/1/163/2d/58848.jpg" alt="" /></a></div>
			<div class="item-price">1,950 RUB</div>
			<div class="item-buy"><a href="">Купить</a></div>
		</li>

		<li>
			<div class="item-name"><a href="">Название няши</a></div>
			<div class="item-img"><a href=""><img src="http://fs06.enter.ru/1/1/163/2d/58848.jpg" alt="" /></a></div>
			<div class="item-price">1,950 RUB</div>
			<div class="item-buy"><a href="">Купить</a></div>
		</li>

		<li>
			<div class="item-name"><a href="">Название няши</a></div>
			<div class="item-img"><a href=""><img src="http://fs06.enter.ru/1/1/163/2d/58848.jpg" alt="" /></a></div>
			<div class="item-price">1,950 RUB</div>
			<div class="item-buy"><a href="">Купить</a></div>
		</li>
	</ul>
</div>

