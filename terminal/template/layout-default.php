<?php
/**
 * @var $page \Terminal\View\DefaultLayout
 */
?><!DOCTYPE HTML>
<html>
<head>
	<link rel="stylesheet/less" type="text/css" href="/terminalStyles/global.less" />
	<script src="/js/less-1.3.3.min.js" type="text/javascript"></script>
	<script data-main="/js/terminal/load" src="/js/require.js"></script>
</head>
<body>
	<div class="bWrap">
    	<?= $page->slotContent() ?>
    </div>
    <div id="console"></div>
</body>
</html>