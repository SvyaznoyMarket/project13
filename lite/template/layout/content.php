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
		    		<aside class="section__left">
		    			<ul class="section-nav">
		    				<li class="section-nav__item"><a href="" class="section-nav__link">Доставка</a></li>
		    				<li class="section-nav__item"><a href="" class="section-nav__link active">Самовывоз</a></li>
		    				<li class="section-nav__item"><a href="" class="section-nav__link">Оплата</a></li>
		    			</ul>
		    			<ul class="section-nav">
		    				<li class="section-nav__item"><a href="" class="section-nav__link">О компании</a></li>
		    				<li class="section-nav__item"><a href="" class="section-nav__link">Правовая информация</a></li>
		    				<li class="section-nav__item"><a href="" class="section-nav__link">Оферта</a></li>
		    			</ul>
		    		</aside>

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
