<?
/**
 * @var $page \View\Main\IndexPage
 * @var $bannerData []
 */


?>

<!doctype html>
<html class="no-js" lang="">

<?= $page->blockHead() ?>

<body>

<?= $page->blockHeader() ?>

<div class="wrapper">
    <?= $page->render('main/content.main.v2') ?>
</div>

<hr class="hr-orange">

<?= $page->blockFooter() ?>

<?= $page->slotBodyJavascript() ?>

<?= $page->blockUserConfig() ?>

<?= $page->blockPopupTemplates() ?>

<div class="overlay js-overlay"></div>

</body>
</html>
