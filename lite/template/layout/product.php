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
		    <div class="middle middle_transform">
		        <div class="container">
		            <main class="content">
		                <div class="content__inner">
						    <?= $page->blockContent() ?>
						</div>
				   </main>
		    	</div>

				<aside class="left-bar">
			        <?= $page->blockNavigation() ?>
			    </aside>
			</div>
		</div>

		<hr class="hr-orange">

		<?= $page->blockFooter() ?>

		<?= $page->slotBodyJavascript() ?>

		<?= $page->blockUserConfig() ?>

	</body>
</html>
