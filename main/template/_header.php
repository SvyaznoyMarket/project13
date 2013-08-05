<?php
/**
 * @var $page           \View\DefaultLayout
 * @var $user           \Session\User
 */
?>

<div class="bSubscribeLightboxPopup clearfix">
    <h3 class="bSubscribeLightboxPopup__eTitle fl">Дружить с нами выгодно!</h3>
    <input class="bSubscribeLightboxPopup__eInput fl" placeholder="Оставьте ваш email и узнайте почему"/>
    <button class="bSubscribeLightboxPopup__eBtn fl" data-url="<?= $page->url('subscribe.create') ?>">Хочу все знать</button>
    <a class="bSubscribeLightboxPopup__eNotNow fr" data-url="<?= $page->url('subscribe.cancel') ?>" href="#">Спасибо, не сейчас</a>
</div>
<!-- Topbar -->
<div class="topbar clearfix">    
    <div class="bRegion">
        <a class="fl" href="<?= $page->url('region.change', ['regionId' => $user->getRegion()->getId()]) ?>" id="jsregion" data-url="<?= $page->url('region.init') ?>" data-region-id="<?= $user->getRegion()->getId() ?>" data-autoresolve-url="<?= $page->url('region.autoresolve') ?>"><?= ((mb_strlen($user->getRegion()->getName()) > 20) ? (mb_substr($user->getRegion()->getName(), 0, 20) . '...') : $user->getRegion()->getName()) ?></a>
        
        <? /*<div class="headerContactPhone fl" >
            <p class="fl headerContactPhone__eTitle">Контакт-cENTER</p>
            <p class="fl headerContactPhone__ePhones"><?= \App::config()->company['phone'] ?>
                <? if (14974 == $user->getRegion()->getId() || 83 == $user->getRegion()->getParentId()): ?>
                <br/><?= \App::config()->company['moscowPhone'] ?>
                <? endif ?>
            </p>
        </div> */ ?>

        <div class="headerContactPhone fl" >
            <p class="fl headerContactPhone__ePhones">
                8 (800) 700-00-09
                <div class="bPhonesRegion fl">
                    8 (495) 775-00-06<br/>
                    8 (812) 703-77-30
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
        <div class="usermenu">
            <?= $page->render('_user') ?>
            <a href="<?= $page->url('cart') ?>" class="hBasket ml10">Моя корзина <span id="topBasket"></span></a>
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
