<?php
/**
 * @var $page \View\Layout
 */
?>

<!-- Lightbox -->
<div class="bBlackBox">
    <div class="bBlackBox__eInner">
        <div class="dropbox" style="left:733px; display:none;">
            <p>Перетащите сюда</p>
        </div>
        <!-- Flybox -->
        <ul class="bBlackBox__eMenu">
            <li class="bBlackBox__eMenuItem bBlackBox__eUser">
                <a href="<?= $page->url('user.login') ?>" class="bBlackBox__eUserLink bBlackBox__eMenuItemLink">Личный кабинет</a>
            </li>
            <li class="bBlackBox__eMenuItem bBlackBox__eCart">
                <a href="<?=  $page->url('cart') ?>" class="bBlackBox__eCartLink bBlackBox__eMenuItemLink"><b class="bBlackBox__eCartQuan"></b>Моя корзина<span class="bBlackBox__eCartTotal">
                    <span class="bBlackBox__eCartSum"></span> &nbsp;<span class="rubl">p</span></span>
                </a>
            </li>
        </ul>
        <div class="flybox bBlackBox__eFlybox mBasket">
            <i class="corner"></i>
            <i class="close" title="Закрыть">Закрыть</i>
        </div>
    </div>
</div>

<!-- новый юзер бар -->

<!--div class="bFixedTopBar">
    <div class="bFixedTopBar__eTop"><a class="bFixedTopBar__eTopLink" href=""><span class="bFixedTopBar__eTopText">Бренды и параметры</span></a></div>

    <div class="bFixedTopBar__eCrumbs">
        <a class="bFixedTopBar__eCrumbsCatImg" href=""><img class="bCatImg" src="http://fs01.enter.ru/6/1/163/27/184686.jpg" /></a>

        <ul class="bFixedTopBar__eCrumbsList">
            <li class="bFixedTopBar__eCrumbsListItem"><a href="">Товары на каждый день</a></li>
            <li class="bFixedTopBar__eCrumbsListItem"><a href="">Товары для дома</a></li>
            <li class="bFixedTopBar__eCrumbsListItem mLast"><strong>Бытовая химия</strong></li>
        </ul>
    </div>

    <div class="bFixedTopBar__eCart">
        <a class="bFixedTopBar__eCartLink" href="">
            <span class="bFixedTopBar__eCartTitle">Корзина</span> <strong class="bFixedTopBar__eCartQuan">5</strong>
            <span class="bFixedTopBar__eCartPrice">74 987 <span class="rubl">p</span></span>
        </a>
    </div>
</div-->

<!--/ новый юзер бар -->

<script type="text/html" id="blackbox_basketshow_tmpl">
    <div class="bBlackBox__eFlyboxInner">
        <div class="font16 pb20">Только что был добавлен в корзину:</div>
        <div class="fl width70">
            <a href="<%=productLink%>">
                <img width="60" height="60" alt="" src="<%=imgSrc%>">
            </a>
        </div>
        <div class="ml70">
            <div class="pb5">
                <a href=""><%=title%></a>
            </div>
            <strong>
                <%=price%>
                <span> &nbsp;</span><span class="rubl">p</span>
            </strong>
        </div>
        <div class="clear pb10"></div>
        <div class="line pb5"></div>
        <div class="fr">Сумма:  <%=totalSum%> <span class="rubl">p</span></div>
        Всего товаров: <%=totalQuan%>
        <div class="clear pb10"></div>
        <div class="ar"> 
            <a class="button bigbuttonlink" value="" href="<%=linkToOrder%>">Оформить заказ</a>
        </div>
    </div>
</script>
<!-- /Lightbox -->