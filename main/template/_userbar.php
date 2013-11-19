<?php
/**
 * @var $page \View\DefaultLayout
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


<div class="fixedTopBar" data-value="<?= $page->json($page->slotUserbarContentData()) ?>">
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
    <a class="fixedTopBar__cartLink">
        <a href="<?=  $page->url('cart') ?>" class="fixedTopBar__cartTitle">Корзина</a> 
        <strong class="fixedTopBar__cartQuan"><%=quantity%></strong>
        <span class="fixedTopBar__cartPrice"><%=sum%> <span class="rubl">p</span></span>

        <? /* ?>
        <div class="fixedTopBar__dd fixedTopBar__cartOn">
            <ul class="cartList">
                <li class="cartList__item">
                    <a class="cartList__itemLink" href=""><img class="cartList__itemImg" src="http://fs01.enter.ru/6/1/163/27/184686.jpg" /></a>
                    <div class="cartList__itemName"><a href="cartList__itemNameLink">Мобильный телефон Explay Power Bank черный</a></div>
                    <div class="cartList__itemInfo">
                        <span class="price">22 190 &nbsp;<span class="rubl">p</span></span>
                        <span class="quan">2 шт.</span>
                        <a href="" class="del">удалить</a>
                    </div>
                </li>

                <li class="cartList__item">
                    <a class="cartList__itemLink" href=""><img class="cartList__itemImg" src="http://fs01.enter.ru/6/1/163/27/184686.jpg" /></a>
                    <div class="cartList__itemName"><a href="cartList__itemNameLink">Мобильный телефон Samsung Champ Neo Duos C3262 белый</a></div>
                    <div class="cartList__itemInfo">
                        <span class="price">22 190 &nbsp;<span class="rubl">p</span></span>
                        <span class="quan">2 шт.</span>
                        <a href="" class="del">удалить</a>
                    </div>
                </li>

                <li class="cartList__item">
                    <a class="cartList__itemLink" href=""><img class="cartList__itemImg" src="http://fs01.enter.ru/6/1/163/27/184686.jpg" /></a>
                    <div class="cartList__itemName"><a href="cartList__itemNameLink">Ноутбук Acer Aspire E1-571G 15,6 500 ГБ i5 3230М GF 710M 1 ГБ Win 8, черный</a></div>
                    <div class="cartList__itemInfo">
                        <span class="price">22 190 &nbsp;<span class="rubl">p</span></span>
                        <span class="quan">2 шт.</span>
                        <a href="" class="del">удалить</a>
                    </div>
                </li>

                <li class="cartList__item">
                    <a class="cartList__itemLink" href=""><img class="cartList__itemImg" src="http://fs01.enter.ru/6/1/163/27/184686.jpg" /></a>
                    <div class="cartList__itemName"><a href="cartList__itemNameLink">Ноутбук Acer Aspire E1-571G 15,6 500 ГБ i5 3230М GF 710M 1 ГБ Win 8, черный</a></div>
                    <div class="cartList__itemInfo">
                        <span class="price">22 190 &nbsp;<span class="rubl">p</span></span>
                        <span class="quan">2 шт.</span>
                        <a href="" class="del">удалить</a>
                    </div>
                </li>

                <li class="cartList__item">
                    <a class="cartList__itemLink" href=""><img class="cartList__itemImg" src="http://fs01.enter.ru/6/1/163/27/184686.jpg" /></a>
                    <div class="cartList__itemName"><a href="cartList__itemNameLink">Мобильный телефон Samsung Champ Neo Duos C3262 белый</a></div>
                    <div class="cartList__itemInfo">
                        <span class="price">22 190 &nbsp;<span class="rubl">p</span></span>
                        <span class="quan">2 шт.</span>
                        <a href="" class="del">удалить</a>
                    </div>
                </li>

                <li class="cartList__item">
                    <a class="cartList__itemLink" href=""><img class="cartList__itemImg" src="http://fs01.enter.ru/6/1/163/27/184686.jpg" /></a>
                    <div class="cartList__itemName"><a href="cartList__itemNameLink">Ноутбук Acer Aspire E1-571G 15,6 500 ГБ i5 3230М GF 710M 1 ГБ Win 8, черный</a></div>
                    <div class="cartList__itemInfo">
                        <span class="price">22 190 &nbsp;<span class="rubl">p</span></span>
                        <span class="quan">2 шт.</span>
                        <a href="" class="del">удалить</a>
                    </div>
                </li>

                <li class="cartList__item">
                    <a class="cartList__itemLink" href=""><img class="cartList__itemImg" src="http://fs01.enter.ru/6/1/163/27/184686.jpg" /></a>
                    <div class="cartList__itemName"><a href="cartList__itemNameLink">Ноутбук Acer Aspire E1-571G 15,6 500 ГБ i5 3230М GF 710M 1 ГБ Win 8, черный</a></div>
                    <div class="cartList__itemInfo">
                        <span class="price">22 190 &nbsp;<span class="rubl">p</span></span>
                        <span class="quan">2 шт.</span>
                        <a href="" class="del">удалить</a>
                    </div>
                </li>
            </ul>

            <div class="transGradWhite"></div> <!-- этот див выводить только если в корзине более 4 товаров, в противном случае display: none; -->

            <div class="btnBuy quickOrder"><a href="" class="btnBuy__eLink quickOrder__link">Оформить заказ</a></div>
        </div>
        <? */ ?>
    </a>
</script>

<script type="text/html" id="userbar_user_tmpl">
    <a href="<%=link%>" class="fixedTopBar__logInLink"><%=name%></a>
    <span class="transGrad"></span>

    <div class="fixedTopBar__dd fixedTopBar__logOut">
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