<?php
/** @var $metas array */
?>
<? foreach ($metas as $name => $content): ?>
    <meta name="<?= $name ?>" content="<?= $content ?>" />
<? endforeach ?>