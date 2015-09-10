<?
/**
 * @var $page \View\ProductCategory\LeafPage
 */
?>

<!doctype html>
<html class="no-js" lang="">
	<?= $page->blockHead() ?>

	<body>
		<?= $page->blockHeader() ?>

	    <div class="wrapper wrapper-content">
	        <?= $page->blockContent() ?>
	    </div>

	    <hr class="hr-orange">

	    <?= $page->blockFooter() ?>

	    <?= $page->blockUserConfig() ?>

	    <?= $page->blockPopupTemplates() ?>
	    <div class="overlay js-overlay"></div>
	    <div class="overlay-transparent js-overlay-transparent"></div>
	</body>
</html>
