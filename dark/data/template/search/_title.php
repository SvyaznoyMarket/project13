<?php
/**
 * @var $page        \View\DefaultLayout
 * @var $searchQuery string
 * @var $meanQuery   string
 * @var $forceMean   string|bool
 * @var $count       int
 */
?>

<? if ($count) { // если товары найдены ?>

    <? if (false == $forceMean) { // если принудительный поиск не был использован ?>
        Ура! Нашли <span class="orange">&quot;<?= $searchQuery ?>&quot;</span>
        <?= $page->helper->formatNumberChoice('{n: n > 10 && n < 20}%count% товаров|{n: n % 10 == 1}%count% товар|{n: n % 10 > 1 && n % 10 < 5}%count% товара|(1,+Inf]%count% товаров', array('%count%' => $count), $count) ?>

        <? } else { // ...иначе, если принудительный поиск использован ?>
        Вы искали <span class="orange">&quot;<?= $meanQuery ?>&quot;</span> ?<br />
        Мы нашли <?= $page->helper->formatNumberChoice('{n: n > 10 && n < 20}%count% товаров|{n: n % 10 == 1}%count% товар|{n: n % 10 > 1 && n % 10 < 5}%count% товара|(1,+Inf]%count% товаров', array('%count%' => $count), $count) ?> :)

        <p class="font16" style="font-family: Tahoma;"><strong><?= $searchQuery ?></strong> мы не нашли. Уточните, пожалуйста, запрос</p>
    <? } ?>

<? } else { // ...иначе, если товары не найдены ?>
Товары не найдены
<? } ?>
