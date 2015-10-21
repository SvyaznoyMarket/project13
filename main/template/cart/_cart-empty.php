<div class="cart cart--empty clearfix">
	<div class="cart-text">В корзине нет товаров</div>
</div>

<? if (\App::abTest()->isOrderWithCart()): ?>
<div class="button-container">
	<a href="<?= $page->url('homepage') ?>" class="button button_action button_size-l">Продолжить покупки</a>
</div>
<? endif ?>