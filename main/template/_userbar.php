<?php
/**
 * @var $page \View\Layout
 */
?>

<!-- Lightbox -->
<!-- <div class="bBlackBox">
    <div class="bBlackBox__eInner">
        <div class="dropbox" style="left:733px; display:none;">
            <p>Перетащите сюда</p>
        </div>
        <ul class="bBlackBox__eMenu">
            <li class="bBlackBox__eMenuItem bBlackBox__eUser">
                <a href="<?//= $page->url('user.login') ?>" class="bBlackBox__eUserLink bBlackBox__eMenuItemLink">Личный кабинет</a>
            </li>
            <li class="bBlackBox__eMenuItem bBlackBox__eCart">
                <a href="<?//=  $page->url('cart') ?>" class="bBlackBox__eCartLink bBlackBox__eMenuItemLink"><b class="bBlackBox__eCartQuan"></b>Моя корзина<span class="bBlackBox__eCartTotal">
                    <span class="bBlackBox__eCartSum"></span> &nbsp;<span class="rubl">p</span></span>
                </a>
            </li>
        </ul>
        <div class="flybox bBlackBox__eFlybox mBasket">
            <i class="corner"></i>
            <i class="close" title="Закрыть">Закрыть</i>
        </div>
    </div>
</div> -->


<div class="fixedTopBar">
    <?= $page->slotUserbarContent() ?>

    <div class="fixedTopBar__cart mEmpty">
        <a class="fixedTopBar__cartLink" href="<?=  $page->url('cart') ?>">
            <span class="fixedTopBar__cartTitle">Корзина</span>
        </a>
    </div>

    <div class="fixedTopBar__logIn mLogin"><!-- Добавляем класс-модификатор mLogin, если пользователь не залогинен -->
        <a href="<?= $page->url('user.login') ?>" class="fixedTopBar__logInLink bAuthLink">Войти</a>
        <span class="transGrad"></span>
    </div>
</div>


<script type="text/html" id="userbar_cart_tmpl">
    <a class="fixedTopBar__cartLink" href="<?=  $page->url('cart') ?>">
        <span class="fixedTopBar__cartTitle">Корзина</span> 
        <strong class="fixedTopBar__cartQuan"><%=quantity%></strong>
        <span class="fixedTopBar__cartPrice"><%=sum%> <span class="rubl">p</span></span>
    </a>
</script>

<script type="text/html" id="userbar_user_tmpl">
    <a href="<%=link%>" class="fixedTopBar__logInLink"><%=name%></a>
    <span class="transGrad"></span>
    <div class="fixedTopBar__logOut">
        <i class="corner"></i>
        <a class="mBtnGrey fixedTopBar__logOutLink" href="/logout">Выйти</a>
    </div>
</script>




<!-- old notificaton -->
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