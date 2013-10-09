<?php
/**
 * @var $page \Terminal\View\DefaultLayout
 */
?><!DOCTYPE HTML>
<html>
<head>
	<link rel="stylesheet/less" type="text/css" href="/terminalStyles/global.less?<?= time() ?>" />
	<script src="/js/prod/less-1.3.3.min.js" type="text/javascript"></script>
	<script data-main="/js/terminal/load" src="/js/prod/require.min.js"></script>
</head>
<body data-connect-terminal="<?= (false !== \App::config()->connectTerminal) ? 'true' : 'false' ?>">
	<div class="bWrap">
    	<?= $page->slotContent() ?>
    </div>
    <div id="console"></div>
</body>
</html>