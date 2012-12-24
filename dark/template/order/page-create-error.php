<?php
/**
 * @var $page      \View\Order\CreatePage
 * @var $user      \Session\User
 * @var $exception \Exception|null
 */
?>

<!-- Header -->
<div class='bBuyingHead'>
    <a href="<?= $page->url('homepage') ?>"></a>
    <i>Оформление заказа</i><br>
    <span>Ошибка</span>
</div>
<!-- /Header -->

<p>
    При оформлении заказа произошла ошибка.<br />
    <a title="Вернуться в корзину для выбора услуг и редактирования количества товаров" alt="Вернуться в корзину для выбора услуг и редактирования количества товаров" href="<?= $page->url('cart') ?>" style="border-color: #4FCBF4; font-weight: bold;" class="motton font14">&lt; Редактировать товары</a>
</p>

<? if (\App::config()->debug && $exception instanceof \Exception): ?>
    <pre><?= $exception ?></pre>
<? endif ?>