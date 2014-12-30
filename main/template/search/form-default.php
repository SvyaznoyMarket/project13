<?php
/**
 * @var $page        \View\Layout
 * @var $searchQuery string
 * @var $isWide      bool
 */
?>

<?
// По запросу Гертмана. Согласовано с Мостицким.
// https://jira.enter.ru/browse/SITE-963
$isWide = (isset($isWide) && $isWide) ? true : false;
if (!isset($searchQuery)) {
    $searchQuery = '';
}

$sHints = $page->getParam('searchHints');

if ($sHints)
foreach ($sHints as $key => $item) {
    if (empty($item) || !is_string($item)) {
        unset($sHints[$key]);
        continue;
    }
    //$sHints[$key] = '<a href="/search?q=' . $item . '" title="Искать...">' . $item . '</a>';
    $sHints[$key] = '<span class="sHint_value">' . $item . '</span>';
}

?>
<form class="search-form clearfix" action="<?= $page->url('search') ?>" method="get">
    <span class="searchtextWrapper">
      <input type="text" autofocus class="searchtext<? if ($isWide) { ?> width483<? } ?> jsSearchInput" name="q" placeholder="Поиск среди десятков тысяч товаров" value="<?= $searchQuery ?>" autocomplete="off"/>
    </span>
    <input type="submit" class="searchbutton" value="Найти" title="Найти" id="try-1"/>
</form>

<? if ($sHints): ?>
    <div class="searchHints">
        <p><span>Например: </span> <?= implode(', ', $sHints); ?></p>
    </div>
<? endif; ?>