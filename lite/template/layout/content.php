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

<div class="wrapper">
    <main class="content">
        <?= $page->render('delivery/content.delivery', $page->getParams()) ?>
    </main>
</div>

<hr class="hr-orange">

<?= $page->blockFooter() ?>

<?= $page->slotBodyJavascript() ?>

<?= $page->blockUserConfig() ?>

<?= $page->blockPopupTemplates() ?>

</body>
</html>
