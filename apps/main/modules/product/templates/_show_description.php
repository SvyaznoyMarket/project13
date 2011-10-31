<div class="goodsinfosmall">
	<div class="font11 gray pb10">Артикул <?php echo $item['article'] ?></div>
	<div class="font14 pb15"><?php echo $item['description'] ?></div>
	<div class="font18 pb10"><?php echo $item['price'] ?> <span class="rubl">p</span></div>
	<div class="goodsbar">
    <?php include_component('cart', 'buy_button', array('product' => $product, 'quantity' => 1)) ?>
    <?php include_component('userDelayedProduct', 'add_button', array('product' => $product)) ?>
    <?php include_component('userProductCompare', 'button', array('product' => $product)) ?>
	</div>
</div>