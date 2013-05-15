<?php
/**
 * @var $page        \View\Layout
 * @var $searchQuery string
 * @var $meanQuery   string
 * @var $forceMean   string|bool
 * @var $count       int
 */
?>

<? if ($count) { // если товары найдены ?>

    <? if (!$forceMean) { // если принудительный поиск не был использован ?>
        Ура! Нашли <span class="orange">&quot;<?= $page->escape($searchQuery) ?>&quot;</span>
        <?= $count . ' ' . $page->helper->numberChoice($count, array('товар', 'товара', 'товаров')) ?>

        <? } else { // ...иначе, если принудительный поиск использован ?>
        Вы искали <span class="orange">&quot;<?= $page->escape($meanQuery) ?>&quot;</span> ?<br />
        Мы нашли <?= $count . ' ' . $page->helper->numberChoice($count, array('товар', 'товара', 'товаров')) ?> :)

        <br />
        <span class="font16" style="font-family: Tahoma;">
            <strong style="font-weight: bold">&quot;<?= $page->escape($searchQuery) ?>&quot;</strong> мы не нашли. Уточните, пожалуйста, запрос
        </span>
    <? } ?>

<? } else { // ...иначе, если товары не найдены ?>
Товары не найдены
<? } ?>
<div id="_searchKiss" style="display: none" data-search='<?=$page->json(['query'=>$searchQuery, 'url'=>$_SERVER['HTTP_REFERER'], 'count'=>$count])?>'></div>
