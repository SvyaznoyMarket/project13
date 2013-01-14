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

    <? if (false == $forceMean) { // если принудительный поиск не был использован ?>
        Ура! Нашли <span class="orange">&quot;<?= $page->escape($searchQuery) ?>&quot;</span>
        <?= $count . ' ' . $page->helper->numberChoice($count, array('товар', 'товара', 'товаров')) ?>

        <? } else { // ...иначе, если принудительный поиск использован ?>
        Вы искали <span class="orange">&quot;<?= $page->escape($meanQuery) ?>&quot;</span> ?<br />
        Мы нашли <?= $count . ' ' . $page->helper->numberChoice($count, array('товар', 'товара', 'товаров')) ?> :)

        <p class="font16" style="font-family: Tahoma;"><strong><?= $page->escape($searchQuery) ?></strong> мы не нашли. Уточните, пожалуйста, запрос</p>
    <? } ?>

<? } else { // ...иначе, если товары не найдены ?>
Товары не найдены
<? } ?>
