<?php
/** @var $page \View\DefaultLayout */
/** @var $title string|null */
/** @var $breadcrumbs array('url' => null, 'name' => null)[] */
?>

<div class="pagehead">
    <? if ((bool)$breadcrumbs): ?>
    <div class="breadcrumbs">
        <a href="/">Enter.ru</a> >
        <? $i = 1; $count = count($breadcrumbs); foreach ($breadcrumbs as $breadcrumb): ?>
            <? if ($i < $count): ?>
                <a href="<?= $breadcrumb['url'] ?>"><?= $breadcrumb['name'] ?></a> &rsaquo;
            <? else: ?>
                <strong><?= $breadcrumb['name'] ?></strong>
            <? endif ?>
        <? $i++; endforeach ?>
    </div>
    <? endif ?>

    <div class="clear"></div>

    <? if ($title): ?><h1><?= $title ?></h1><? endif ?>

    <noindex>
        <div class="searchbox">
            <?= $page->render('form-search') ?>
        </div>
    </noindex>
    <div class="clear"></div>
</div>
