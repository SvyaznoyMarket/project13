<?php
/**
 * @var $page \View\DefaultLayout
 */
?>

<div class="userbar userbar-fixed topbarfix topbarfix-fx js-topbar-fixed" data-value="<?= $page->json($page->slotUserbarContentData()) ?>">
    <?= $page->render('userbar2/topbarContent') ?>
</div>