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
<div class="topbar clearfix">  
    <a class="topbar_loc jsChangeRegion" href="<?= $page->url('region.change', ['regionId' => $user->getRegion()->getId()]) ?>" data-url="<?= $page->url('region.init') ?>" data-region-id="<?= $user->getRegion()->getId() ?>" data-autoresolve-url="<?= $page->url('region.autoresolve', ['nocache' => 1]) ?>">
        <?= ((mb_strlen($user->getRegion()->getName()) > 20) ? (mb_substr($user->getRegion()->getName(), 0, 20) . '...') : $user->getRegion()->getName()) ?>
    </a>

    <div class="topbar_call" itemscope itemtype="http://schema.org/Organization">
        <div class="topbar_call_t">Звонок с сайта</div>

        <div class="topbar_call_lst">
            <span class="topbar_call_i" itemprop="telephone"><?= \App::config()->company['phone'] ?></span>
            <span class="topbar_call_i" itemprop="telephone"><?= \App::config()->company['moscowPhone'] ?></span>
        </div>
    </div>

    <? if (\App::config()->onlineCall['enabled']): ?>
        <a class="bCall" onclick="typeof(_gaq)=='undefined'?'':_gaq.push(['_trackEvent', 'Zingaya', 'ButtonClick']);typeof(_gat)=='undefined'?'':_gat._getTrackerByName()._setAllowLinker(true); window.open(typeof(_gat)=='undefined'?this.href+'?referrer='+escape(window.location.href):_gat._getTrackerByName()._getLinkerUrl(this.href+'?referrer='+escape(window.location.href)), '_blank', 'width=236,height=220,resizable=no,toolbar=no,menubar=no,location=no,status=no'); return false" href="http://zingaya.com/widget/e990d486d664dfcff5f469b52f6bdb62">Позвонить онлайн</a>
    <? endif ?>

    <div class="topbar_lks">
        <a class="topbar_lks_i" href="<?= $page->url('shop') ?>">Магазины Enter</a>
        <a class="topbar_lks_i" href="/how_get_order">Доставка</a>
    </div>

    <div class="bSubscribeLightboxPopupNotNow mFl"></div>

    <noindex>
    <div class="topbarfix topbarfix-stc <?=('homepage'==\App::request()->attributes->get('route') || isset($scheme) && $scheme === 'homepage'?'topbarfix-home':null)?>">
        <div class="topbarfix_cart mEmpty">
            <a href="/cart" class="topbarfix_cart_tl">Корзина</a>
        </div>
        
        <!-- добавляем, если добавили к сравнению товар topbarfix_cmpr-full -->
        <div class="topbarfix_cmpr">
            <a href="" class="topbarfix_cmpr_tl">Сравнение</a>
            <span class="topbarfix_cmpr_qn">10</span>
        </div>
        
        <!-- Добавляем класс-модификатор topbarfix_log-unl, если пользователь не залогинен -->
        <div class="topbarfix_log topbarfix_log-unl">
            <a href="/login" class="topbarfix_log_lk bAuthLink">Личный кабинет</a>
            <?= $page->slotUserbarEnterprize() ?>
        </div>
    </div>
    </noindex>

    <a class="topbar_ep" href=""></a>   
</div>
<!-- /Topbar -->

<!-- Header -->
<div id="header" class="clearfix">
    <a id="topLogo" href="/">Enter Связной</a>
    <?= $page->slotMainMenu() ?>
</div>
<!-- /Header -->

<script id="userbar_cart_empty_tmpl" type="text/html">
    <div class="topbarfix_cart mEmpty">
        <a href="/cart" class="topbarfix_cart_tl">Корзина</a>
    </div>
</script>

<script id="userbar_cart_tmpl" type="text/html">
    <a href="<?=  $page->url('cart') ?>" class="topbarfix_cart_tl">
        <span class="topbarfix_cart_tx">Корзина</span>
        <strong class="topbarfix_cart_qn">{{quantity}}</strong>
    </a>

    <div class="topbarfix_dd topbarfix_cartOn">
        {{#hasProducts}}
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
        {{/hasProducts}}

        {{#showTransparent}}
            <div class="transGradWhite"></div> <!-- этот див выводить только если в корзине более 3 товаров, в противном случае display: none; -->
        {{/showTransparent}}

        <div class="btnBuy quickOrder"><a href="<?= $page->url('order') ?>" class="btnBuy__eLink quickOrder__link">Оформить заказ</a></div>
    </div>

    <div class="hintDd"><!-- если похожии товары есть то добавляем класс mhintDdOn -->
    </div>
</script>

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

<!-- Данные пользователя -->
<script id="userbar_user_tmpl" type="text/html">
    <a href="{{link}}" class="topbarfix_log_lk{{#hasEnterprizeCoupon}} enterprizeMember{{/hasEnterprizeCoupon}}">{{name}}</a>

    <div class="topbarfix_dd topbarfix_logOut">
        {{^hasEnterprizeCoupon}}<?= $page->slotUserbarEnterprizeContent() ?>{{/hasEnterprizeCoupon}}

        <a class="mBtnGrey topbarfix_logOutLink" href="<?= $page->url('user.logout') ?>">Выйти</a>
    </div>
</script>
