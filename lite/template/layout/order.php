<?
/**
 * @var $page \View\OrderV3\Layout
 */
?>

<!doctype html>
<html class="no-js" lang="">

<?= $page->blockHead() ?>

<body>

<div class="wrapper">
    <?= $page->blockOrderHead() ?>
    <?= $page->blockContent() ?>
</div>

<?= $page->blockModulesDefinitions() ?>

<?= $page->slotBodyJavascript() ?>

<?= $page->blockUserConfig() ?>

<?= $page->blockAuth() ?>

</body>
</html>
