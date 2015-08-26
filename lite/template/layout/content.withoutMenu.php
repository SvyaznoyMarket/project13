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
		    	<div class="section section_aside">
		    		<section class="section__right">
		    			<div class="static-content section__right-inner">
		        			<?= $page->blockContent() ?>
		        		</div>
		        	</section>
		        </div>
		    </main>
		</div>

		<hr class="hr-orange">

		<?= $page->blockFooter() ?>

		<?= $page->slotBodyJavascript() ?>

		<?= $page->blockUserConfig() ?>

		<?= $page->blockPopupTemplates() ?>

	</body>
</html>
