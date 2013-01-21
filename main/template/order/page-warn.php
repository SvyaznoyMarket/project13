<?php
/**
 * @var $page      \View\Order\CreatePage
 * @var $user      \Session\User
 * @var $errorData array
 */
?>

<!-- Header -->
<div class='bBuyingHead'>
    <a href="<?= $page->url('homepage') ?>"></a>
    <i>Оформление заказа</i><br>
    <span>Уточнение количества товаров</span>
</div>
<!-- /Header -->

<input id="product_errors" type="hidden" data-value="<?= $page->json($errorData) ?>" />
<input id="cart-link" type="hidden" data-value="<?= $page->url('cart') ?>" />


<div id="order-loader" class='bOrderPreloader'>
    <span>Загрузка...</span><img src='/images/bPreloader.gif'>
</div>