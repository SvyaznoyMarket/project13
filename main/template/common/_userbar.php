<?php
/**
 * @var $page \View\DefaultLayout
 */
?>

<div class="userbar userbar--fixed js-topbar-fixed <? if ('product' == \App::request()->attributes->get('route')): ?>userbar--pp<? endif ?>" data-value="<?= $page->json($page->slotUserbarContentData()) ?>">
	<div class="userbar__inn">
		<ul class="userbtn js-topbarfix">
			<?= $page->render('userbar/_userinfo') ?>
			<?= $page->render('userbar/_usercompare') ?>
			<?= $page->render('userbar/_usercart') ?>
		</ul>

		<?= $page->slotUserbarContent() ?>

		<div class="ep-fixed js-pp-ep-fishka">
	        <div class="ep-fixed__fishka">%</div>
	        <div class="ep-fixed__desc">Фишка со скидкой 20% на этот товар</div>
	    </div>
	</div>
</div>
