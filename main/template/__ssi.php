<?php

return function(
    \Helper\TemplateHelper $helper,
    $path
) {
    $queryString = \App::request()->getQueryString();
    $path = '/' . trim($path, '/'); // защита от ошибки
    if (!empty($queryString)) $path .= '&'.$queryString;
?>
<!--# include virtual="/ssi.php?path=<?= $path ?>" -->
<? };