<?php
/**
 * @var $page \View\Layout
 */
?>

<form class="search-form" action="<?= $page->url('search') ?>" method="get">
    <input name="q" type="text" class="text startse" value="Поиск среди 30 000 товаров" />
    <input type="submit" class="searchbutton" value="Найти" title="Найти" />
</form>