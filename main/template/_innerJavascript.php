<?php
/**
 * @var $page \View\DefaultLayout
 */
?>

<? if (\App::config()->analytics): ?>
<script type="text/javascript">
    (function() {
        var s=document.createElement("script");s.src='http://crossss.com/crossssInfo.aspx?id=<?= \App::config()->crossss['id'] ?>';s.type="text/javascript";document.getElementsByTagName("HEAD")[0].appendChild(s);
    })();
</script>
<? endif ?>

<?= $page->slotSociomantic() ?>
<?= $page->slotRetailRocket() ?>
<?= $page->slotCriteo() ?>
<?= $page->slotAdmitad() ?>
<?= $page->slotMarinLandingPageTagJS() ?>
<?= $page->slotMarinConversionTagJS() ?>

<? if (\App::config()->debug): ?>
    <!-- // <script src="http://<?= \App::config()->mainHost ?>:35729/js/livereload.js" type="text/javascript"></script> -->
<? endif ?>