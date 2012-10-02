<?php
/** @var $stylesheets string[] */
?>

<? foreach ($stylesheets as $stylesheet): ?>
    <link href="<?= $stylesheet ?>" type="text/css" rel="stylesheet" media="screen" />
<? endforeach ?>