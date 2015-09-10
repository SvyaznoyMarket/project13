<?php
/**
 * @var string $searchQuery
 */

$helper = new \Helper\TemplateHelper();

?>

<p class="searchEmpty">Товары не найдены</p>

<? if (\App::config()->product['showRelated']): ?>
    <?= $helper->render('product/__slider', [
        'type'           => 'search',
        'title'          => 'Возможно, вам подойдут',
        'products'       => [],
        'limit'          => \App::config()->product['itemsInSlider'],
        'page'           => 1,
        'url'            => $page->url('search.recommended', ['q' => $searchQuery]),
        'sender'         => [
            'name'     => 'retailrocket',
            'position' => 'SearchNoResults',
        ],
    ]) ?>
<? endif ?>