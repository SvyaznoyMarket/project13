<?php
/**
 * @var $page        \View\Layout
 * @var $searchQuery string
 * @var $isWide      bool
 */
?>

<?php
// if (empty($searchQuery)) $searchQuery = 'Поиск среди ' . number_format(\App::config()->product['totalCount'], 0, ',', ' ') . ' товаров';
// $isWide = (isset($isWide) && $isWide) ? true : false;

// По запросу Гертмана. Согласовано с Мостицким.
// https://jira.enter.ru/browse/SITE-963
$searchQuery = 'Поиск среди десятков тысяч товаров';
$isWide = (isset($isWide) && $isWide) ? true : false;
?>

<form class="search-form clearfix" action="<?= $page->url('search') ?>" method="get">
    <span class="searchtextWrapper">
      <input type="text" class="searchtext<? if ($isWide) { ?> width483<? } ?>" name="q" placeholder="<?= $searchQuery ?>" autocomplete="off"/>
    </span>
    <input type="submit" class="searchbutton" value="Найти" title="Найти" id="try-1"/>
</form>