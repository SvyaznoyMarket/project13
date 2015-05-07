<?php
/**
 * @var $page \View\DefaultLayout
 */
?>

<div class="userbar userbar-fixed topbarfix topbarfix-fx js-topbar-fixed <? if ('product' == \App::request()->attributes->get('route')): ?>mProdCard<? endif ?>" data-value="<?= $page->json($page->slotUserbarContentData()) ?>">
	<?= $page->render('userbar/_userbar', ['class' => 'userbtn js-topbarfix']) ?>
	<?= $page->slotUserbarContent() ?>
</div>
