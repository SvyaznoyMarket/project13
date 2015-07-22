<?
/**
 * @var $page \View\Cart\IndexPage
 */
?>

<!doctype html>
<html class="no-js" lang="">

<?= $page->blockHead() ?>

<body>

<?= $page->blockHeader() ?>

<div class="wrapper">
    <div class="middle">
        <aside class="left-bar">
            <?= $page->blockNavigation() ?>
        </aside>
    </div>
</div>

<hr class="hr-orange">

<?= $page->blockFooter() ?>

<?= $page->slotBodyJavascript() ?>

<?= $page->blockUserConfig() ?>

</body>
</html>
