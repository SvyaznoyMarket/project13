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

<!-- фиксированный вспомогательный блок для каталога -->
<div class="fixedTopBar">
    <div class="fixedTopBar__up">
        <a class="fixedTopBar__upLink" href="">
            <em class="cornerTop">&#9650;</em> Бренды и параметры
        </a>
    </div>

    <div class="fixedTopBar__crumbs">
        <a class="fixedTopBar__crumbsImg" href=""><img class="crumbsImg" src="http://fs01.enter.ru/6/1/163/27/184686.jpg" /></a>

        <ul class="fixedTopBar__crumbsList">
            <li class="fixedTopBar__crumbsListItem"><a class="fixedTopBar__crumbsListLink" href="">Товары на каждый день</a></li>
            <li class="fixedTopBar__crumbsListItem"><a class="fixedTopBar__crumbsListLink" href="">Товары для дома</a></li>
            <li class="fixedTopBar__crumbsListItem mLast">Бытовая химия</li>
        </ul>
    </div>

    <div class="fixedTopBar__cart"><!-- Добавляем класс-модификатор mEmpty, если карзина пуста -->
        <a class="fixedTopBar__cartLink" href="">
            <span class="fixedTopBar__cartTitle">Корзина</span> 
            <strong class="fixedTopBar__cartQuan">5</strong>
            <span class="fixedTopBar__cartPrice">74 987 <span class="rubl">p</span></span>
        </a>
    </div>

    <div class="fixedTopBar__logIn"><!-- Добавляем класс-модификатор mLogin, если пользователь не залогинен -->
        <a href="" class="fixedTopBar__logInLink">Бурлакова Таня Владимировна</a>
        <span class="transGrad"></span>
    </div>
</div>
<!--/ Фиксированный вспомогательный блок для каталога -->

<? /*
<!-- фиксированный вспомогательный блок для карточки товара -->
<div class="fixedTopBar mProdCard">
    <div class="fixedTopBar__crumbs">
        <div class="fixedTopBar__crumbsImg"><img class="crumbsImg" src="http://fs01.enter.ru/6/1/163/27/184686.jpg" /></div>

        <div class="wrapperCrumbsList">
            <ul class="fixedTopBar__crumbsList">
                <li class="fixedTopBar__crumbsListItem"><a href="">Товары на каждый день</a></li>
                <li class="fixedTopBar__crumbsListItem mLast">Смартфон Samsung Galaxy Mega 6.3 8 ГБ GT-I9200 белый</li>
            </ul>

            <div class="transGradWhite"></div>
        </div>
    </div>

    <div class="fixedTopBar__buy">
        <div class="bPrice"><strong class="jsPrice">9 490</strong> <span class="rubl">p</span></div>

        <div class="bCountSection clearfix" data-spinner-for="">
            <button class="bCountSection__eM">-</button>
            <input class="bCountSection__eNum" type="text" value="1">
            <button class="bCountSection__eP">+</button>
            <span>шт.</span>
        </div><!--/counter -->

        <div class="bWidgetBuy__eBuy btnBuy">
            <a href="" class="btnBuy__eLink jsBuyButton" data-group="">Купить</a>
        </div>
    </div>

    <div class="fixedTopBar__cart"><!-- Добавляем класс-модификатор mEmpty, если карзина пуста -->
        <a class="fixedTopBar__cartLink" href="">
            <span class="fixedTopBar__cartTitle">Корзина</span> 
            <strong class="fixedTopBar__cartQuan">5</strong>
            <span class="fixedTopBar__cartPrice">74 987 <span class="rubl">p</span></span>
        </a>
    </div>

    <div class="fixedTopBar__logIn"><!-- Добавляем класс-модификатор mLogin, если пользователь не залогинен -->
        <a href="" class="fixedTopBar__logInLink">Бурлакова Таня Владимировна</a>
        <span class="transGrad"></span>
    </div>
</div>
<!--/ Фиксированный вспомогательный блок для карточки товара -->
*/?>

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