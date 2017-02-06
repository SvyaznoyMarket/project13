<?php
/**
 * @param string|int $offset Число или селектор элемента, задающие отступ, после прокрутке до которого появится ссылка "Наверх"
 */
return function(
    \Helper\TemplateHelper $helper,
    $offset = 600,
    $showWhenFullCartOnly = false
) { ?>

    <a class="upper js-upper" href="#" data-offset="<?= $helper->escape($offset) ?>" data-show-when-full-cart-only="<?= $helper->escape($showWhenFullCartOnly) ?>">Наверх</a>

<? };