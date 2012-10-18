<?php
/**
 * @var $page        \View\Layout
 * @var $searchQuery string
 * @var $isWide      bool
 */
?>

<?php
if (empty($searchQuery)) $searchQuery = 'Поиск среди ' . number_format(\App::config()->product['totalCount'], 0, ',', ' ') . ' товаров';
$isWide = (isset($isWide) && $isWide) ? true : false;
?>

<form class="search-form" action="<?= $page->url('search') ?>" method="get">
    <input type="text" class="searchtext<? if ($isWide) { ?> width483<? } ?>" name="q" value="<?= $searchQuery ?>"/>
    <input type="submit" class="searchbutton" value="Найти" title="Найти" id="try-1"/>
</form>