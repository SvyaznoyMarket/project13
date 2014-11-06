<?php
/**
 * @var $page           \View\DefaultLayout
 * @var $user           \Session\User
 */
?>

<!-- Topbar -->
<?= $page->render('userbar/topbar') ?>
<!-- /Topbar -->

<!-- Header -->
<div id="header" class="clearfix">
    <a id="topLogo" href="/">Enter Связной</a>
    <?= $page->slotMainMenu() ?>
</div>
<!-- /Header -->

<!-- Окно с информацией о товаре только что положенном в корзину -->
<script id="buyinfo_tmpl" type="text/html">
    <div class="topbarfix_dd topbarfix_cartOn">
        <ul class="cartLst">
            {{#products}}
                <li class="cartLst_i">
                    <a class="cartLst_lk" href="{{url}}"><img class="cartLst_img" src="{{image}}" /></a>
                    <div class="cartLst_n"><a href="{{url}}">{{name}}</a></div>
                    <div class="cartLst_inf">
                        <span class="price">{{formattedPrice}} &nbsp;<span class="rubl">p</span></span>
                        <span class="quan">{{quantity}} шт.</span>
                        <a href="{{deleteUrl}}" class="del jsCartDelete">удалить</a>
                    </div>
                </li>
            {{/products}}
         </ul>

        {{#showTransparent}}
            <div class="transGradWhite"></div> <!-- этот див выводить только если в корзине более 3 товаров, в противном случае display: none; -->
        {{/showTransparent}}
        <div class="btnBuy quickOrder"><a href="<?= $page->url('order') ?>" class="btnBuy__eLink quickOrder__link">Оформить заказ</a></div>
    </div>
</script>
