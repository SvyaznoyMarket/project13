<?php
/**
 * @var $page           \View\DefaultLayout
 * @var $user           \Session\User
 */
?>

<div class="header_t clearfix">
    <div class="header_i hdcontacts">
        <a class="hdcontacts_lk jsChangeRegion undrl" href="<?= $page->url('region.change', ['regionId' => $user->getRegion()->getId()]) ?>"><?= $user->getRegion()->getName() ?></a>
        <div class="hdcontacts_phone">+7 (495) 775-00-06</div>
    </div>

    <? if (\App::config()->onlineCall['enabled']): ?>
        <a class="header_i hdcall" href="http://zingaya.com/widget/e990d486d664dfcff5f469b52f6bdb62" onclick="typeof(_gaq)=='undefined'?'':_gaq.push(['_trackEvent', 'Zingaya', 'ButtonClick']);typeof(_gat)=='undefined'?'':_gat._getTrackerByName()._setAllowLinker(true); window.open(typeof(_gat)=='undefined'?this.href+'?referrer='+escape(window.location.href):_gat._getTrackerByName()._getLinkerUrl(this.href+'?referrer='+escape(window.location.href)), '_blank', 'width=236,height=220,resizable=no,toolbar=no,menubar=no,location=no,status=no'); return false">
            <i class="i-header i-header-phone"></i>
            <span class="hdcall_tx">Звонок<br/>с сайта</span>
        </a>
    <? endif ?>

    <ul class="header_i hdlk">
        <li class="hdlk_i"><a href="<?= $page->url('shop') ?>" class="hdlk_lk undrl">Наши магазины</a></li>
        <li class="hdlk_i"><a href="/how_get_order" class="hdlk_lk undrl">Доставка</a></li>
    </ul>

    <menu class="header_i userbtn js-topbarfix">

        <!--<li class="userbtn_i userbtn_i-lk userbtn_i-act userbtn_i-ep">
            <a class="userbtn_lk" href=""><span class="undrl">Войти</span></a>
        </li>

        <li class="userbtn_i userbtn_i-act">
            <span class="userbtn_lk">
                <i class="userbtn_icon i-header i-header-compare"></i>
                <span class="userbtn_tx">Сравнение</span>
                <span class="userbtn_count">1</span>
            </span>
        </li>

        <li class="userbtn_i userbtn_i-act userbtn_i-cart">
            <a class="userbtn_lk userbtn_lk-cart" href="">
                <i class="userbtn_icon i-header i-header-cart"></i>
                <span class="userbtn_tx">Корзина</span>
                <span class="userbtn_count">2</span>
            </a>
        </li>-->

        <?= $page->render('userbar/_userinfo') ?>
        <?= $page->render('userbar/_usercompare') ?>
        <?= $page->render('userbar/_usercart') ?>
    </menu>
</div>
