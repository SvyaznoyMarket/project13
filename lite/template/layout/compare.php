<?
/**
 * @var $page \View\Compare\CompareLayout
 */
?>

<!doctype html>
<html class="no-js" lang="">

<?= $page->blockHead() ?>

<body>

<div class="wrapper-nofixed">
	<?= $page->blockHeader() ?>

	<?= $page->blockContent() ?>
</div>

<?= $page->blockModulesDefinitions() ?>

<?= $page->slotBodyJavascript() ?>

<?= $page->blockUserConfig() ?>

</body>
</html>
