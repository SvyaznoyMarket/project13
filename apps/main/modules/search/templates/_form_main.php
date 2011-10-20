<form class="search-form" action="<?php echo url_for('search', array('searchString' => $searchString)) ?>" method="get">
    <input name="q" type="text" class="text startse" value="Поиск среди 20 000 товаров" />
    <input type="submit" class="searchbutton" value="Найти" title="Найти" />
</form>