<?php
/**
 * @var $user \Session\User
 */
?>

<? if (\App::config()->analytics['enabled'] && ($cusId = \PartnerCounter\Odinkod::getCusId($user->getRegion()))): ?>
<script language="javascript">
    var odinkod = {
        "type": "homepage"
    };
    document.write('<scr'+'ipt src="'+('https:' == document.location.protocol ? 'https://ssl.' : 'http://') +
        'cdn.odinkod.ru/tags/<?= $cusId ?>.js"></scr'+'ipt>');
</script>
<? endif ?>