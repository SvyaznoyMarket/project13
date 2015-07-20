<?
/**
 * @var $page \View\Product\IndexPage
 */
?>

<!doctype html>
<html class="no-js" lang="">

<?= $page->blockHead() ?>

<body>

<div class="wrapper">

    <?= $page->blockHeader() ?>

    <hr class="hr-orange">

    <?= $page->blockContent() ?>

</div>

<hr class="hr-orange">

<?= $page->blockFooter() ?>

<?= $page->blockAuth() ?>

<?= $page->slotBodyJavascript() ?>

<?= $page->blockUserConfig() ?>

</body>
</html>
