<?php
/**
 * @var $page               \View\DefaultLayout
 * @var $rootCategoryInMenu \Model\Product\Category\TreeEntity
 * @var $catalogConfig      array
 * @var $slideData          array
 * @var $content            string
 */


$helper = new \Helper\TemplateHelper();
$siblingCategories = $rootCategoryInMenu ? $rootCategoryInMenu->getChild() : [];

if ((bool)$siblingCategories) {
    /* <!-- TCHIBO - слайдер-меню разделов Чибо --> */
    echo $helper->render('product-category/__sibling-list',
        [
            'categories' => $siblingCategories, // категории-соседи
            'catalogConfig' => $catalogConfig
        ]);
    /* <!--/ TCHIBO - слайдер-меню разделов Чибо -->*/
}

?>
<div id="promoCatalog" class="bPromoCatalog" data-slides="<?= $page->json($slideData) ?>" data-use-interval="true">

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
    <div class="tchiboCatalogInner">
        <a href=""><img class="tchiboCatalog__img" src="/styles/tchiboCatalog/img/man.jpg" /></a>

        <div class="tchiboCatalog__title">
            <a class="titleCat" href="">Мужчины</a>

            <ul class="tchiboCatalog__list">
                <li class="item"><a class="link" href="">Классика</a></li>
                <li class="item"><a class="link" href="">Мужской сезон</a></li>
                <li class="item"><a class="link" href="">Модные тренды</a></li>
            </ul><!--/ список подкатегории -->
        </div>
    </div><!--/ категория -->

    <div class="tchiboCatalogInner">
        <a href=""><img class="tchiboCatalog__img" src="/styles/tchiboCatalog/img/woman.jpg" /></a>

        <div class="tchiboCatalog__title">
            <a class="titleCat" href="">Женщины</a>

            <ul class="tchiboCatalog__list">
                <li class="item"><a class="link" href="">Классика</a></li>
                <li class="item"><a class="link" href="">Модные тренды</a></li>
                <li class="item"><a class="link" href="">100% натуральное</a></li>
            </ul><!--/ список подкатегории -->
        </div>
    </div><!--/ категория -->

    <div class="tchiboCatalogInner">
        <a href=""><img class="tchiboCatalog__img" src="/styles/tchiboCatalog/img/kids.jpg" /></a>

        <div class="tchiboCatalog__title">
            <a class="titleCat" href="">Дети</a>

            <ul class="tchiboCatalog__list">
                <li class="item"><a class="link" href="">Идеальный завтрак</a></li>
                <li class="item"><a class="link" href="">Уход за одеждой и шитье</a></li>
            </ul><!--/ список подкатегории -->
        </div>
    </div><!--/ категория -->

    <div class="tchiboCatalogInner">
        <a href=""><img class="tchiboCatalog__img" src="/styles/tchiboCatalog/img/home.jpg" /></a>

        <div class="tchiboCatalog__title">
            <a class="titleCat" href="">Дом</a>

            <ul class="tchiboCatalog__list">
                <li class="item"><a class="link" href="">Чистота дома</a></li>
                <li class="item"><a class="link" href="">Идеальный завтрак</a></li>
                <li class="item"><a class="link" href="">Уход за одеждой и шитье</a></li>
            </ul><!--/ список подкатегории -->
        </div>
    </div><!--/ категория -->

    <div class="tchiboCatalogInner">
        <a href=""><img class="tchiboCatalog__img" src="/styles/tchiboCatalog/img/sport.jpg" /></a>

        <div class="tchiboCatalog__title">
            <a class="titleCat" href="">Спорт</a>

            <ul class="tchiboCatalog__list">
                <li class="item"><a class="link" href="">Твои рекорды</a></li>
                <li class="item"><a class="link" href="">Активный отдых</a></li>
                <li class="item"><a class="link" href="">Фитнес</a></li>
            </ul><!--/ список подкатегории -->
        </div>
    </div><!--/ категория -->

    <div class="tchiboCatalogInner">
        <a href=""><img class="tchiboCatalog__img" src="/styles/tchiboCatalog/img/kids.jpg" /></a>

        <div class="tchiboCatalog__title">
            <a class="titleCat" href="">Дети</a>

            <ul class="tchiboCatalog__list">
                <li class="item"><a class="link" href="">Идеальный завтрак</a></li>
                <li class="item"><a class="link" href="">Уход за одеждой и шитье</a></li>
            </ul><!--/ список подкатегории -->
        </div>
    </div><!--/ категория -->

    <div class="tchiboCatalogInnerBanner">
        <a href=""><img class="tchiboCatalog__img" src="/styles/tchiboCatalog/img/cofee.jpg" /></a>
    </div> <!--/ вывод баннера или категории без списка подкатегорий и верхней плашкой-заголовком -->
</div>
<!--/ TCHIBO - каталог разделов, баннеров, товаров Чибо -->