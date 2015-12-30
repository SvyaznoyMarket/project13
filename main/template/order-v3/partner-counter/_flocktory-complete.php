<?php
return function (
    \Helper\TemplateHelper $helper
) {
?>
    <? if (\App::config()->flocktory['postcheckout']): ?>
        <div class="js-orderV3New-complete-flocktory-postcheckout"></div>
    <? endif ?>
<? } ?>
