<?php

return function(
    \Helper\TemplateHelper $helper,
    $path,
    $query = []
) {
    $path = '/' . trim($path, '/'); // защита от ошибки
?>
<!--# include virtual="/ssi-cached.php?<?= http_build_query(array_merge($query, ['path' => $path])) ?>" -->
<? };