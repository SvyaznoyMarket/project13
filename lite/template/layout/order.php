<?
/**
 * @var $page \View\OrderV3\Layout
 */
?>

<!doctype html>
<html class="no-js" lang="">

<?= $page->blockHead() ?>

<body>

    <?= $page->blockOrderHead() ?>

	<div class="wrapper wrapper-order">
	    <?= $page->blockContent() ?>
	</div>

<?= $page->blockModulesDefinitions() ?>

<?= $page->slotBodyJavascript() ?>

<?= $page->blockUserConfig() ?>

<?= $page->blockAuth() ?>

</body>
</html>
