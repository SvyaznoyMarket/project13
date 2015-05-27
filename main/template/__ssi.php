<?php

return function(
    \Helper\TemplateHelper $helper,
    $path
) {
    $path = '/' . trim($path, '/'); // защита от ошибки
?>
<!--# include virtual="/ssi.php?path=<?= $path ?>" -->
<? };