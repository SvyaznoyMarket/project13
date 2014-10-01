<?php
/**
 * @var $page           \View\DefaultLayout
 */
?>

<div class="topbarfix_log topbarfix_log-unlogin js-topbarfixLogin" data-bind="visible: !name()">
    <a href="/login" class="topbarfix_log_lk bAuthLink">Личный кабинет</a>
    <?= $page->slotUserbarEnterprize() ?>
</div>

<div class="topbarfix_log topbarfix_log-login js-topbarfixLogin" data-bind="visible: name()" style="display: none">
    <a href="" class="topbarfix_log_lk" data-bind="attr: { href: link }, css: { enterprizeMember: hasEnterprizeCoupon }">
        <!--ko text: firstName--><!--/ko--><br />
        <!--ko text: lastName--><!--/ko-->
    </a>

    <div class="topbarfix_dd topbarfix_logOut">
        <!-- ko ifnot: hasEnterprizeCoupon -->
        <?= $page->slotUserbarEnterprizeContent() ?>
        <!-- /ko -->
        <a class="mBtnGrey topbarfix_logOutLink" href="<?= $page->url('user.logout') ?>">Выйти</a>
    </div>
</div>
