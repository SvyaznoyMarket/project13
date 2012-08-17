<?php
#JSON data
	$json = array (
		'jsref' => $item->token,
		'jstitle' => htmlspecialchars($item->name, ENT_QUOTES, 'UTF-8'),
		'jsprice' => $order['price'],
		'jsimg' => $item->getMainPhotoUrl(3)
	)
?>
<div class="goodsinfosmall" ref="<?php echo $order['token'] ?>" data-value='<?php echo json_encode( $json ) ?>'>
	<div class="font11 gray pb10">Артикул <?php echo $order['article'] ?></div>
	<div class="font14 pb15"><?php echo $order['description'] ?></div>
	<div class="font18 pb10"><?php echo $order['price'] ?> <span class="rubl">p</span></div>
	<div class="goodsbar mSmallBtns">
    <?php include_component('cart', 'buy_button', array('product' => $item, 'quantity' => 1)) ?>
	</div>
</div>