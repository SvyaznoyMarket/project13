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
        <a class="bSubscribeLightboxPopup__eLink" href="/enterprize">выбрать</a>
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
    <div class="bRegion">
        <a class="fl jsChangeRegion bRegion__eLink" href="<?= $page->url('region.change', ['regionId' => $user->getRegion()->getId()]) ?>" data-url="<?= $page->url('region.init') ?>" data-region-id="<?= $user->getRegion()->getId() ?>" data-autoresolve-url="<?= $page->url('region.autoresolve', ['nocache' => 1]) ?>"><?= ((mb_strlen($user->getRegion()->getName()) > 20) ? (mb_substr($user->getRegion()->getName(), 0, 20) . '...') : $user->getRegion()->getName()) ?></a>
        
        <? /*<div class="headerContactPhone fl" >
            <p class="fl headerContactPhone__eTitle">Контакт-cENTER</p>
            <p class="fl headerContactPhone__ePhones"><?= \App::config()->company['phone'] ?>
                <? if (14974 == $user->getRegion()->getId() || 83 == $user->getRegion()->getParentId()): ?>
                <br/><?= \App::config()->company['moscowPhone'] ?>
                <? endif ?>
            </p>
        </div> */ ?>

        <div itemscope itemtype="http://schema.org/Organization" class="headerContactPhone fl" >
            <p class="fl headerContactPhone__ePhones">
                <span itemprop="telephone"><?= \App::config()->company['phone'] ?></span><br/>
                <div class="bPhonesRegion fl">
                    <span itemprop="telephone"><?= \App::config()->company['moscowPhone'] ?></span><br/>
                    <span itemprop="telephone">8 (812) 703-77-30</span>
                </div>
            </p>
        </div>

        <? if (\App::config()->onlineCall['enabled']): ?>
            <a class="bCall" onclick="typeof(_gaq)=='undefined'?'':_gaq.push(['_trackEvent', 'Zingaya', 'ButtonClick']);typeof(_gat)=='undefined'?'':_gat._getTrackerByName()._setAllowLinker(true); window.open(typeof(_gat)=='undefined'?this.href+'?referrer='+escape(window.location.href):_gat._getTrackerByName()._getLinkerUrl(this.href+'?referrer='+escape(window.location.href)), '_blank', 'width=236,height=220,resizable=no,toolbar=no,menubar=no,location=no,status=no'); return false" href="http://zingaya.com/widget/e990d486d664dfcff5f469b52f6bdb62">Позвонить онлайн</a>
        <? endif ?>

        <a class="headerShopLink" href="<?= $page->url('shop') ?>">Магазины Enter</a>
        <div class="bSubscribeLightboxPopupNotNow mFl"></div>
    </div>
    <noindex>
        <div class="fixedTopBar mStatic <? if ('homepage' == \App::request()->attributes->get('route')):?> mHomepage<? endif ?>">
            <div class="fixedTopBar__cart mEmpty">
                <a href="/cart" class="fixedTopBar__cartTitle">Корзина</a>
            </div>

            <div class="fixedTopBar__logIn mLogin"><!-- Добавляем класс-модификатор mLogin, если пользователь не залогинен -->
                <a href="/login" class="fixedTopBar__logInLink bAuthLink">Личный кабинет</a>
                <span class="transGrad"></span>

                <?= $page->slotUserbarEnterprize() ?>
            </div>
        </div>
    </noindex>
</div>
<!-- /Topbar -->

<!-- Header -->
<div id="header" class="clearfix">
    <a id="topLogo" href="/">Enter Связной</a>
    <?= $page->slotMainMenu() ?>
</div>
<!-- /Header -->

<?= $page->slotBrandMenu() ?>

<script id="userbar_cart_empty_tmpl" type="text/html">
    <div class="fixedTopBar__cart mEmpty">
        <a href="/cart" class="fixedTopBar__cartTitle">Корзина</a>
    </div>
</script>

<script id="userbar_cart_tmpl" type="text/html">
    <a href="<?=  $page->url('cart') ?>" class="fixedTopBar__cartTitle">
        <span class="fixedTopBar__cartText">Корзина</span>
        <strong class="fixedTopBar__cartQuan">{{quantity}}</strong>
        <span class="fixedTopBar__cartPrice">{{sum}} <span class="rubl">p</span></span>
    </a>

    <div class="fixedTopBar__dd fixedTopBar__cartOn">
        {{#hasProducts}}
            <ul class="cartList">
                {{#products}}
                    <li class="cartList__item">
                        <a class="cartList__itemLink" href="{{url}}"><img class="cartList__itemImg" src="{{image}}" /></a>
                        <div class="cartList__itemName"><a href="{{url}}">{{name}}</a></div>
                        <div class="cartList__itemInfo">
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
    <div class="fixedTopBar__dd fixedTopBar__cartOn">
        <ul class="cartList">
            {{#products}}
                <li class="cartList__item">
                    <a class="cartList__itemLink" href="{{url}}"><img class="cartList__itemImg" src="{{image}}" /></a>
                    <div class="cartList__itemName"><a href="{{url}}">{{name}}</a></div>
                    <div class="cartList__itemInfo">
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
    <a href="{{link}}" class="fixedTopBar__logInLink{{#hasEnterprizeCoupon}} enterprizeMember{{/hasEnterprizeCoupon}}"><span class="name__hidden">{{name}}</span></a>
    <span class="transGrad"></span>

    <div class="fixedTopBar__dd fixedTopBar__logOut">
        {{^hasEnterprizeCoupon}}<?= $page->slotUserbarEnterprizeContent() ?>{{/hasEnterprizeCoupon}}

        <a class="mBtnGrey fixedTopBar__logOutLink" href="<?= $page->url('user.logout') ?>">Выйти</a>
    </div>
</script>
