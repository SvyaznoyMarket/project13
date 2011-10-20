<!-- Goods info -->
<div class="goodsphotosmall"><div class="photo"><img src="<?php echo $product->getMainPhotoUrl(2) ?>" alt="" width="163" height="163" title="" /></div></div>
<div class="fr width219">
	<div class="font11 gray pb10">Перейти в:</div>
	<div class="articlemenu">
		<ul>
			<li><a href="<?php echo url_for('productCard', $sf_data->getRaw('product')) ?>">Карточка товара</a></li>
			<li class="next"><a href="<?php echo url_for('productComment', $sf_data->getRaw('product')) ?>" class="current">Отзывы пользователей</a></li>
<!--			<li class="next"><a href="<?php echo url_for('productStock', $sf_data->getRaw('product')) ?>">Где купить в магазинах</a></li>-->
		</ul>
	</div>
</div>

<div class="goodsinfosmall">
	<div class="font11 gray pb10">Артикул <?php echo $product->article ?></div>
	<div class="font14 pb15"><?php echo $product->description ?></div>
	<div class="font18 pb10"><?php echo $product->getFormattedPrice() ?> <span class="rubl">p</span></div>
	<div class="goodsbar">
    <?php include_component('cart', 'buy_button', array('product' => $product, 'quantity' => 1)) ?>
    <?php include_component('userDelayedProduct', 'add_button', array('product' => $product)) ?>
    <?php include_component('userProductCompare', 'button', array('product' => $product)) ?>
	</div>
</div>
<!-- /Goods info -->

<div class="clear pb20"></div>