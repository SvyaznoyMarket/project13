<?php
/** @var $javascripts string[] */
?>

<? foreach ($javascripts as $javascript): ?>
    <script src='<?= $javascript ?>' type='text/javascript'></script>
<? endforeach ?>