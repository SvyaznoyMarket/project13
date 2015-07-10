<?php
/**
 * @var $page \View\DefaultLayout
 */
?>

<div class="userbar userbar--fixed js-topbar-fixed" data-value="<?= $page->json($page->slotUserbarContentData()) ?>">
	<div class="userbar__inn">
	    <?= $page->render('userbar2/topbarContent') ?>
    </div>
</div>