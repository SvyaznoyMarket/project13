<?php
/**
 * @var $page \View\DefaultLayout
 */
?>

<div class="userbar userbar--fixed topbarfix topbarfix-fx js-topbar-fixed <? if ('product' == \App::request()->attributes->get('route')): ?>mProdCard<? endif ?>" data-value="<?= $page->json($page->slotUserbarContentData()) ?>">
	<div class="userbar__inn">
		<ul class="userbtn js-topbarfix">
			<?= $page->render('userbar/_userinfo') ?>
			<?= $page->render('userbar/_usercompare') ?>
			<?= $page->render('userbar/_usercart') ?>
		</ul>

		<?= $page->slotUserbarContent() ?>
	</div>
</div>
