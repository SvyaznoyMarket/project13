<?php
/**
 * @var $page      \View\DefaultLayout
 * @var $promo     \Model\Promo\Entity
 * @var $slideData array
 */
?>
<div class="clear"></div>
<!-- promo catalog -->
<div id="promoCatalog" class="bPromoCatalog" data-slides="<?= $page->json($slideData) ?>">
    <style>
    .allpromo {font-family: 'Enter Type Bold'; font-size:20px; padding:0 0 30px 30px}
    .allpromo a {color:#49c7ed} 
    </style>
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

    <div class="allpromo">Посмотреть все <a href="http://www.enter.ru/special_offers ">wow-акции &gt;</a></div>
    <div class="allpromo">Рассказать друзьям
        <script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
        <div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="none" data-yashareQuickServices="vkontakte,facebook,twitter"></div>
    </div>
</div>
<!-- end promo catalog -->
