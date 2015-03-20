<?php
/**
 * @var $page           \View\DefaultLayout
 * @var $user           \Session\User
 * @var $subscribeForm   array
 */
?>

<? if (!\App::config()->enterprize['enabled']): ?>
    <div class="bSubscribeLightboxPopup clearfix">
        <h3 class="bSubscribeLightboxPopup__eTitle fl"><?= $subscribeForm['mainText'] ?></h3>
        <div class="fl"><input type="text" class="bSubscribeLightboxPopup__eInput fl" placeholder="<?= $subscribeForm['inputText'] ?>"/></div>
        <button class="bSubscribeLightboxPopup__eBtn fl" data-url="<?= $page->url('subscribe.create') ?>"><?= $subscribeForm['buttonText'] ?></button>
        <a class="bSubscribeLightboxPopup__eNotNow fr" data-url="#" href="#">Спасибо, не сейчас</a>
    </div>
<? endif ?>
