<?php

return function(
    \Helper\TemplateHelper $helper,
    $searchQuery,
    $meanQuery,
    $forceMean,
    $count,
    \Model\Product\Category\Entity $category = null
) { ?>

    <? if ($count) { // если товары найдены ?>

        <? if (!$forceMean) { // если принудительный поиск не был использован ?>
        <h1 class="bTitlePage js-pageTitle">
            Нашли <span class="orange">&quot;<?= $helper->escape($searchQuery) ?>&quot;</span>
            <? if ($category): ?>
                в категории "<?= $category->getName() ?>"
            <? endif ?>
            <!--<?//= $count . ' ' . $helper->numberChoice($count, ['товар', 'товара', 'товаров']) ?>-->
        </h1>

        <? } else { // ...иначе, если принудительный поиск использован ?>
        <h1 class="bTitlePage js-pageTitle">
            Вы искали <span class="orange">&quot;<?= $helper->escape($meanQuery) ?>&quot;</span> ?<br />
            Мы нашли <?= $count . ' ' . $helper->numberChoice($count, ['товар', 'товара', 'товаров']) ?> :)

            <br />
            <span class="font16" style="font-family: Tahoma;">
            <strong style="font-weight: bold">&quot;<?= $helper->escape($searchQuery) ?>&quot;</strong> мы не нашли. Уточните, пожалуйста, запрос
        </span>
        </h1>
        <? } ?>
    
    <? }/* else { // ...иначе, если товары не найдены ?>
        Товары не найдены
    <? }*/ // Зачем дублировать? Аналогичный текст выводится в template/product/list/_compact.mustache ?>

<? };