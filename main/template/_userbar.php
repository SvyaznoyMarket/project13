<?php
/**
 * @var $page \View\DefaultLayout
 */
?>

<div class="topbarfix topbarfix-fx <? if ('product' == \App::request()->attributes->get('route')): ?>mProdCard<? endif ?>" data-value="<?= $page->json($page->slotUserbarContentData()) ?>">

    <?= $page->render('userbar/_usercart') ?>

    <?= $page->render('userbar/_usercompare') ?>

    <?= $page->render('userbar/_userinfo') ?>

    <?= $page->slotUserbarContent() ?>
</div>
