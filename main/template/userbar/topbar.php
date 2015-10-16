<?php
/**
 * @var $page           \View\DefaultLayout
 */
?>

<div class="userbar userbar-static topbar clearfix js-topbar">
    <div class="topbar_r">
        <noindex>
            <?= $page->render('userbar/_userbar', ['class' => 'userbtn topbarfix topbarfix-stc js-topbarfix js-topbar-static ' . ('homepage' == \App::request()->attributes->get('route') || isset($scheme) && $scheme === 'homepage' ? 'topbarfix-home' : null)]) ?>
        </noindex>
    </div>

    <?= $page->render('userbar/_defaultContent') ?>
</div>