<?php
#JSON data
	$json = array (
		'jsref' => $product->token,
		'jstitle' => htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'),
		'jsprice' => $item['price'],
		'jsimg' => $product->getMainPhotoUrl(3)
	)
?>
<div class="goodsinfosmall" ref="<?php echo $item['token'] ?>" data-value='<?php echo json_encode( $json ) ?>'>
	<div class="font11 gray pb10">Артикул <?php echo $item['article'] ?></div>
	<div class="font14 pb15"><?php echo $item['description'] ?></div>
	<div class="font18 pb10"><?php echo $item['price'] ?> <span class="rubl">p</span></div>
	<div class="goodsbar mSmallBtns">
    <?php include_component('cart', 'buy_button', array('product' => $product, 'quantity' => 1)) ?>
	</div>
</div>