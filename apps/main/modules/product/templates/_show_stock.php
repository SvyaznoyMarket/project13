<?php
#JSON data
	$json = array (
		'jsref' => $item->token,
		'jstitle' => htmlspecialchars($item->name, ENT_QUOTES, 'UTF-8'),
		'jsprice' => $order['price'],
		'jsimg' => $item->getMainPhotoUrl(3)
	)
?>
<div class='bInShop__eArticul'>Артикул #<?php echo $order['article'] ?></div>
<div class='bInShop__eImage'><img src="<?php echo $order['photo'] ?>" alt="" width="160" height="160" title="" /></div>

<div class='bInShop__ePrice'><span class="price"><?php echo $order['price'] ?></span> <span class="rubl">p</span></div>
<div class='bInShop__eButton' ref="<?php echo $order['token'] ?>" data-value='<?php echo json_encode( $json ) ?>'>
  <?php include_component('cart', 'buy_button', array('product' => $item, 'quantity' => 1, 'view' => 'add')) ?>
</div>
<p class='bInShop__eDescription'><?php echo $order['description'] ?></p>
<div class='bInShop__eButton'><a href="<?php echo $order['url'] ?>" class='bWhiteButton'>Подробнее о товаре</a></div>