<?php
/**
 * @var $page           \View\DefaultLayout
 */
?>

<div class="userbar userbar-static topbar clearfix js-topbar">
    <div class="topbar_r">
        <noindex>
            <ul class="userbtn topbarfix topbarfix-stc js-topbarfix js-topbar-static <?=('homepage'==\App::request()->attributes->get('route') || isset($scheme) && $scheme === 'homepage'?'topbarfix-home':null)?>">
                <?= $page->render('userbar/_userinfo') ?>
                <?= $page->render('userbar/_usercompare') ?>
                <?= $page->render(\App::config()->wikimart['enabled'] ? 'userbar/_usercart-wikimart' : 'userbar/_usercart') ?>
            </ul>
        </noindex>
    </div>

    <?= $page->render('userbar/_defaultContent') ?>
</div>