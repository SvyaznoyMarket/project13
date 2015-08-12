<?
/**
 * @var $page \View\Product\IndexPage
 */
?>

<!doctype html>
<html class="no-js" lang="">

	<?= $page->blockHead() ?>

	<body>
		<?= $page->blockHeader() ?>

		<div class="wrapper">
            <main class="content">
				    <?= $page->blockContent() ?>
		   </main>
		</div>

		<hr class="hr-orange">

		<?= $page->blockFooter() ?>

		<?= $page->slotBodyJavascript() ?>

		<?= $page->blockUserConfig() ?>

		<?= $page->blockPopupTemplates() ?>

	</body>
</html>
