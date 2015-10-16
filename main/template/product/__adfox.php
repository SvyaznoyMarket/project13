<?php

return function(
    \Model\Product\Entity $product
) {
    if (!\App::config()->adFox['enabled']) return '';

    $adfox220 = 'adfox400';
?>

<? if (\App::config()->adFox['enabled']): ?>
    <div class="adfoxWrapper" id="<?= $adfox220 ?>"></div>
<? endif ?>

<? };