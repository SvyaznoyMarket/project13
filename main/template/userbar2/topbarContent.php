<?php
/**
 * @var $page           \View\DefaultLayout
 * @var $user           \Session\User
 */
?>

<?
$helper = \App::helper();
?>
<div class="header_t clearfix">
    <div class="header_i hdcontacts">
        <a class="hdcontacts_lk jsChangeRegion undrl" href="<?= $page->url('region.change', ['regionId' => $user->getRegion()->getId()]) ?>"><?= $user->getRegion()->getName() ?></a>
        <? if (!$user->isRegionChoosed()): ?>
            <?= $helper->renderWithMustache('region/confirm', ['region' => [
                'id' => $user->getRegion()->id,
                'name' => $user->getRegion()->name,
            ]]) ?>
        <? endif ?>
        <div class="hdcontacts_phone"><?=\App::helper()->escape($helper->regionalPhone())?></div>
    </div>

    <? if (\App::config()->onlineCall['enabled']): ?>
        <noindex>
            <a class="header_i hdcall" rel="nofollow" href="//zingaya.com/widget/e990d486d664dfcff5f469b52f6bdb62" onclick="window.open(this.href+'?referrer='+escape(window.location.href), '_blank', 'width=236,height=220,resizable=no,toolbar=no,menubar=no,location=no,status=no'); return false">
                <i class="i-header i-header-phone"></i>
                <span class="hdcall_tx">Звонок<br/>с сайта</span>
            </a>
        </noindex>
    <? endif ?>

    <ul class="header_i hdlk">
        <li class="hdlk_i"><a href="<?= $page->url('shop') ?>" class="hdlk_lk undrl">Пункты выдачи заказов</a></li>
        <? if (time() > 1451322300): // SITE-6508 ?>
            <li class="hdlk_i"><a href="/delivery" class="hdlk_lk undrl">Доставка</a></li>
        <? endif ?>
        <li class="hdlk_i"><a href="/how_pay" class="hdlk_lk undrl">Оплата</a></li>
    </ul>

    <!--noindex-->
    <?= $page->render('userbar/_userbar', ['class' => 'header_i userbtn js-topbarfix']) ?>
    <!--/noindex-->
</div>
