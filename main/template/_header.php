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
<?= $page->slotTopbar() ?>
<!-- /Topbar -->

<!-- Header -->
<div id="header" class="clearfix">
    <a id="topLogo" href="/">Enter Связной</a>
    <?= $page->slotMainMenu() ?>
</div>
<!-- /Header -->
