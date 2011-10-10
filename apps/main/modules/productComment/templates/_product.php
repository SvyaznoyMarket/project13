<!-- Goods info -->
<div class="goodsphotosmall"><i class="bestseller">Бестселлер</i><a href=""><img src="http://mobiguru.ru/f/image/2/4/6/2/6/24626186_samsung_i9000_galaxy_s_1.jpg" alt="" width="160" height="160" title="" /></a></div>
<div class="fr width219">
	<div class="font11 gray pb10">Перейти в:</div>
	<div class="articlemenu">
		<ul>
			<li><a href="<?php echo url_for('productCard', $sf_data->getRaw('product')) ?>">Карточка товара</a></li>
			<li><a href="<?php echo url_for('productComment', $sf_data->getRaw('product')) ?>" class="current">Отзывы пользователей</a></li>
			<li class="next"><a href="<?php echo url_for('productStock', $sf_data->getRaw('product')) ?>">Где купить в магазинах</a></li>
		</ul>
	</div>
</div>

<div class="goodsinfosmall">
	<div class="font11 gray pb10">Артикул <?php echo $product->article ?></div>
	<div class="font14 pb15"><?php echo $product->description ?></div>
	<div class="font18 pb10"><?php echo $product->price ?> <span class="rubl">p</span></div>
	<div class="goodsbar">
		<a href="<?php echo url_for('cart_add', array('product' => $product->token, 'quantity' => 1)) ?>" class="link1" title="добавить в корзину"></a>
		<a href="<?php echo url_for('userDelayedProduct_create', $sf_data->getRaw('product')) ?>" class="link2" title="добавить в избранное"></a>
		<a href="<?php echo url_for('userProductCompare_add', $sf_data->getRaw('product')) ?>" class="link3" title="сравнить"></a>
	</div>
</div> 
<!-- /Goods info -->   

<div class="clear pb20"></div>