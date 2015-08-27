<?
/**
 * @var $page \View\Content\DeliveryMapPage
 */
?>

<!doctype html>
<html class="no-js" lang="">
	<?= $page->blockHead() ?>

	<body>

		<?= $page->blockHeader() ?>

		<div class="wrapper wrapper-content">
		    <main class="content">
		    	<section class="section">
		        	<?= $page->blockContent() ?>
		        </section>
		    </main>
		</div>

		<hr class="hr-orange">

		<?= $page->blockFooter() ?>

		<?= $page->slotBodyJavascript() ?>

		<?= $page->blockUserConfig() ?>

		<?= $page->blockPopupTemplates() ?>

	</body>
</html>
