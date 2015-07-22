<?
/**
 * @var $page \View\ProductCategory\LeafPage
 */
?>

<!doctype html>
<html class="no-js" lang="">

<?= $page->blockHead() ?>

<body>

	<?= $page->blockHeader() ?>

    <div class="wrapper">

        <?= $page->blockContent() ?>

    </div>

    <hr class="hr-orange">

    <?= $page->blockFooter() ?>

    <?= $page->slotBodyJavascript() ?>

    <?= $page->blockUserConfig() ?>

</body>
</html>
