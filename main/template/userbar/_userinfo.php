<?php
/**
 * @var $page           \View\DefaultLayout
 */
?>

<li class="userbtn_i topbarfix_log topbarfix_log-unlogin" data-bind="visible: !name()">
    <a href="/login" class="topbarfix_log_lk bAuthLink"><span class="topbarfix_log_tx">Вход</span></a>
</li>

<li class="userbtn_i topbarfix_log topbarfix_log-login js-topbarfixLogin" data-bind="visible: name(), css: {'enterprizeMember': isEnterprizeMember}" style="display: none">
    <a href="" class="topbarfix_log_lk" data-bind="attr: { href: link }">
        <!--ko text: firstName--><!--/ko--> <!--ko text: lastName--><!--/ko-->
    </a>

    <div class="userbar-dd topbarfix_logOut">
        <a class="btn-type btn-type--normal btn-type--longer" href="<?= $page->url('user.logout') ?>">Выйти</a>
    </div>
</li>
