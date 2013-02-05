<?php
/**
 * @var $page \Terminal\View\DefaultLayout
 */
?><!DOCTYPE HTML>
<html>
	<link rel="stylesheet/less" type="text/css" href="/terminalStyles/global.less" />
	<script src="/js/less-1.3.3.min.js" type="text/javascript"></script>
<head>
    <?= $page->slotJavascript() ?>
</head>
<body>

    <?= $page->slotContent() ?>

</body>
</html>