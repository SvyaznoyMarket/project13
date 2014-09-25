<?php
/**
 * @var $page           \View\DefaultLayout
 */
?>

<!-- Добавляем класс-модификатор topbarfix_log-unl, если пользователь не залогинен -->
<div class="topbarfix_log topbarfix_log-unl" data-bind="css: { 'topbarfix_log-unl': typeof name() !== 'string' }">

    <!-- ko ifnot: name -->
    <a href="/login" class="topbarfix_log_lk bAuthLink">Личный кабинет</a>
    <?= $page->slotUserbarEnterprize() ?>
    <!-- /ko -->

    <!-- ko if: name -->
    <a href="" class="topbarfix_log_lk" data-bind="text: name, attr: { href: link }, css: { enterprizeMember: hasEnterprizeCoupon }"></a>
    <div class="topbarfix_dd topbarfix_logOut">
        <!-- ko ifnot: hasEnterprizeCoupon -->
        <?= $page->slotUserbarEnterprizeContent() ?>
        <!-- /ko -->
        <a class="mBtnGrey topbarfix_logOutLink" href="<?= $page->url('user.logout') ?>">Выйти</a>
    </div>
    <!-- /ko -->

</div>
