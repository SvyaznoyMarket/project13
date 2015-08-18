<?
/**
 * @var $searchQuery    string      Поисковый запрос
 * @var $meanQuery      string      Автозамена запроса $searchQuery (при неправильной раскладке, например)
 * @var $productCount   int         Количество найденных товаров
 */
?>

<div class="search-result-status">

    <? if ($productCount != 0 && !$meanQuery) : ?>
    <div class="search-result-status__title">
        Вы искали <span class="link-color"><?= $searchQuery ?></span>
    </div>
    <? endif ?>

    <? if (!isset($productCount) || $productCount == 0) : ?>
    <div class="search-result-status-notfound">
        <div class="search-result-status-notfound__title">Товары не найдены</div>
        <div class="search-result-status-notfound__text mb-15">Попробуйте изменить поисковый запрос</div>
        <div class="search-result-status-notfound__text">Или позвоните нам по телефону <span class="search-result-status-notfound__phone"><?= \App::config()->company['phone'] ?></span></div>
        <div class="search-result-status-notfound__text">мы поможем подобрать товар</div>
    </div>
    <? endif ?>

    <? if ($productCount != 0 && $meanQuery) : ?>
    <div class="search-result-status__title">
        Показаны результаты поиска по запросу <span class="link-color">смартфон</span>
    </div>

    <div class="search-result-status__text">По запросу <span class="link-color"><?= $searchQuery ?></span> товары не найдены</div>
    <div class="search-result-status__text">Попробуйте изменить поисковый запрос</div>
    <? endif ?>
</div>