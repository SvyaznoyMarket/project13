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

	<hr class="hr-orange">
</div>

<?= $page->blockModulesDefinitions() ?>

<?= $page->slotBodyJavascript() ?>

<?= $page->blockUserConfig() ?>

</body>
</html>
