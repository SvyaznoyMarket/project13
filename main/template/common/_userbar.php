<?php
/**
 * @var $page \View\DefaultLayout
 */
?>

<menu class="userbar userbar-fixed topbarfix js-topbarfix topbarfix-fx <? if ('product' == \App::request()->attributes->get('route')): ?>mProdCard<? endif ?>" data-value="<?= $page->json($page->slotUserbarContentData()) ?>">


	<menu class="userbtn">
		<?= $page->render('userbar/_userinfo') ?>

	    <?= $page->render('userbar/_usercompare') ?>

	    <?= $page->render('userbar/_usercart') ?>
	</menu>

	<?= $page->slotUserbarContent() ?>
</menu>
