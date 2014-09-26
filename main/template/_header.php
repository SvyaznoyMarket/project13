<?php
/**
 * @var $page           \View\DefaultLayout
 * @var $user           \Session\User
 * @var $subscribeForm   array
 */
?>

<? if (\App::config()->enterprize['enabled']): ?>
    <div class="bSubscribeLightboxPopup mEnterPrize clearfix">
        <img class="bSubscribeLightboxPopup__eImg fl" src="/css/subscribeLightboxPopup/img/epLogo.png" />
        <h3 class="bSubscribeLightboxPopup__eTitle fl">Лучшие предложения Enter для клиентов</h3>
        <a class="bSubscribeLightboxPopup__eLink" href="<?= $page->url('enterprize', ['from' => 'enterprize-top-banner']) ?>">выбрать</a>
        <a class="bSubscribeLightboxPopup__eNotNow fr" data-url="<?= $page->url('subscribe.cancel') ?>" href="#">Спасибо, не сейчас</a>
    </div>
<? else: ?>
    <div class="bSubscribeLightboxPopup clearfix">
        <h3 class="bSubscribeLightboxPopup__eTitle fl"><?= $subscribeForm['mainText'] ?></h3>
        <div class="fl"><input type="text" class="bSubscribeLightboxPopup__eInput fl" placeholder="<?= $subscribeForm['inputText'] ?>"/></div>
        <button class="bSubscribeLightboxPopup__eBtn fl" data-url="<?= $page->url('subscribe.create') ?>"><?= $subscribeForm['buttonText'] ?></button>
        <a class="bSubscribeLightboxPopup__eNotNow fr" data-url="<?= $page->url('subscribe.cancel') ?>" href="#">Спасибо, не сейчас</a>
    </div>
<? endif ?>

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
