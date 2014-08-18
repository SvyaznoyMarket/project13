<?php
return function(
    \Helper\TemplateHelper $helper,
    $error
) {
?>

    <div id="OrderV3ErrorBlock" style="display: <?= $error ? 'block' : 'none'?>">
        <?= $error ?>
    </div>

<? } ?>