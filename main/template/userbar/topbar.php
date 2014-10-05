<?php
/**
 * @var $page           \View\DefaultLayout
 */
?>

<div class="userbar userbar-static topbar clearfix js-topbar">
    <div class="topbar_r">
        <noindex>
            <div class="topbarfix js-topbarfix topbarfix-stc <?=('homepage'==\App::request()->attributes->get('route') || isset($scheme) && $scheme === 'homepage'?'topbarfix-home':null)?>">

                <?= $page->render('userbar/_usercart') ?>

                <?= $page->render('userbar/_usercompare') ?>

                <?= $page->render('userbar/_userinfo') ?>

            </div>
        </noindex>
    </div>
    
    <?= $page->render('userbar/_defaultContent') ?>
</div>