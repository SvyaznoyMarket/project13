<?php
return function(
    \Helper\TemplateHelper $helper,
    $error
) {
?>

    <div id="OrderV3ErrorBlock" class="errtx" style="display: <?= $error ? 'block' : 'none'?>">
        <?= $error ?>
    </div>

<? } ?>