<?php
/**
 * @var $page           \View\DefaultLayout
 * @var $user           \Session\User
 * @var $rootCategories \Model\Product\Category\Entity[]
 */
?>

<!-- Topbar -->
<div class="topbar">
    <div class="bRegion">
        <a href="<?= $page->url('region.change', array('regionId' => $user->getRegion()->getId())) ?>" id="jsregion" data-url="<?= $page->url('region.init') ?>"><?= $user->getRegion()->getName() ?></a>
        <b>Контакт-cENTER <?= \App::config()->company['phone'] ?></b>

        <? if (\App::config()->onlineCall['enabled']): ?>
            <a class="bCall" onclick="typeof(_gaq)=='undefined'?'':_gaq.push(['_trackEvent', 'Zingaya', 'ButtonClick']);typeof(_gat)=='undefined'?'':_gat._getTrackerByName()._setAllowLinker(true); window.open(typeof(_gat)=='undefined'?this.href+'?referrer='+escape(window.location.href):_gat._getTrackerByName()._getLinkerUrl(this.href+'?referrer='+escape(window.location.href)), '_blank', 'width=236,height=220,resizable=no,toolbar=no,menubar=no,location=no,status=no'); return false" href="http://zingaya.com/widget/e990d486d664dfcff5f469b52f6bdb62">Позвонить онлайн</a>
        <? endif ?>

        <a href="<?= $page->url('shop') ?>">Магазины Enter</a>
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
<div id="header" class="topmenu newYearTheme">
    <a id="topLogo" href="/">Enter Связной</a>
    <?= $page->slotRootCategory() ?>
</div>
<!-- /Header -->