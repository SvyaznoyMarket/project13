<?php
/**
 * @var $user \Session\User
 */
?>

<?
// закрыл из-за SITE-1400
?>
<? if (false && \App::config()->analytics['enabled'] && ($cusId = \Partner\Counter\Etargeting::getCusId($user->getRegion()))): ?>
<script language="javascript">
    var odinkod = {
        "type": "homepage"
    };
    document.write('<scr'+'ipt src="'+('https:' == document.location.protocol ? 'https://ssl.' : 'http://') +
        'cdn.odinkod.ru/tags/<?= $cusId ?>.js"></scr'+'ipt>');
</script>
<? endif ?>