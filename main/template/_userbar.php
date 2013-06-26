<?php
/**
 * @var $page \View\Layout
 */
?>

<!-- Lightbox -->
<div class="lightbox">
    <div class="lightboxinner">
        <div class="dropbox" style="left:733px; display:none;">
            <p>Перетащите сюда</p>
        </div>
        <!-- Flybox -->
        <ul class="lightboxmenu">
            <li class="fl">
                <a href="<?//= $page->url(user.login) ?>" class="point point1"><b></b>Личный кабинет</a>
            </li>
            <li>
                <a href="<?=  $page->url(cart) ?>" class="point point2"><b></b>Моя корзина<span class="total" style="display:none;">
                    <span id="sum"></span> &nbsp;<span class="rubl">p</span></span>
                </a>
            </li>
        </ul>
    </div>
</div>

<script type="text/html" id="blackBoxBasketShow">
    <div class="flybox">
        <div class="font16 pb20">Только что был добавлен в корзину:</div>
        <div class="fl width70">
            <a href="">
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
        Всего товаров:  <%=TotalQuan%>
        <div class="clear pb10"></div>
        <div class="ar"> 
            <a class="button bigbuttonlink" value="" href="<%=linkToOrder%>">Оформить заказ</a>
        </div>
    </div>
</script>
<!-- /Lightbox -->