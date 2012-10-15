<?php
/** @var $page \View\DefaultLayout */
/** @var $title string|null */
/** @var $breadcrumbs array('url' => null, 'name' => null)[] */
?>

<div class="pagehead">

    <?php echo $page->render('_breadcrumbs', array('breadcrumbs' => $breadcrumbs, 'class' => 'breadcrumbs')) ?>

    <div class="clear"></div>

    <? if ($title): ?><h1><?= $title ?></h1><? endif ?>

    <noindex>
        <div class="searchbox">
            <?= $page->render('search/form-default') ?>
        </div>
    </noindex>
    <div class="clear"></div>
</div>
