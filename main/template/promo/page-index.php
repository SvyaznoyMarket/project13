<?php
/**
 * @var $page      \View\DefaultLayout
 * @var $promo     \Model\Promo\Entity
 * @var $slideData array
 */
?>

<!-- promo catalog -->
<div id="promoCatalog" class="bPromoCatalog" data-slides="<?= $page->json($slideData) ?>">

    <script type="text/html" id="slide_tmpl">
        <div class="bPromoCatalogSliderWrap_eSlide">
            <a class="bPromoCatalogSliderWrap_eSlideLink topLifted" href="<%=linkUrl%>">
                <div class="bottomLifted"><img src="<%=imgUrl%>" alt="<%=title%>"/></div>
            </a>
        </div>
    </script>

    <h2 class="bPromoCatalog_eName">промо каталог <span class="bPromoCatalog_eSecondName"><?= $promo->getName() ?></span></h2>

    <div class="bPromoCatalogSlider">
        <a href="#" class="bPromoCatalogSlider_eArrow mArLeft"></a>
        <a href="#" class="bPromoCatalogSlider_eArrow mArRight"></a>
        <div class="bPromoCatalogSlider_eFadePane mLeft"></div>
        <div class="bPromoCatalogSlider_eFadePane mRight"></div>
        <div class="bPromoCatalogSliderWrap clearfix"></div>
    </div>

    <div id="promoCatalogPaginator" class="bPaginator clearfix"></div>
</div>
<!-- end promo catalog -->
