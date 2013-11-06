<?php

return function(
    \Helper\TemplateHelper $helper,
    $searchQuery
) {

    $links = [];

    $links[] = [
        'url'  => $helper->url('search', ['q' => $searchQuery]),
        'name' => 'Поиск "' . $helper->escape($searchQuery) . '"',
        'last' => true,
    ];
?>

    <?= $helper->renderWithMustache('_breadcrumbs', ['links' => $links]) ?>

<? };