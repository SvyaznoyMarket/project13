<?php
return function(
    \Helper\TemplateHelper $helper,
    $error
) {
    if ($error) : ?>

    <div class="error">
        <?= $error ?>
    </div>

<? endif; } ?>