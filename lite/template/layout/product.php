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

		<div class="wrapper wrapper-content">
            <main class="content">
				    <?= $page->blockContent() ?>
		   </main>
		</div>

		<hr class="hr-orange">

		<?= $page->blockFooter() ?>

		<?= $page->blockUserConfig() ?>

		<?= $page->blockPopupTemplates() ?>
		<div class="overlay js-overlay"></div>
	</body>
</html>
