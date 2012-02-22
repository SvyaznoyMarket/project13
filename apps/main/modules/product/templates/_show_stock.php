<?php
#JSON data
	$json = array (
		'jsref' => $product->token,
		'jstitle' => htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'),
		'jsprice' => $item['price'],
		'jsimg' => $product->getMainPhotoUrl(3)
	)
?>
<div class='bInShop__eArticul'>Артикул #<?php echo $item['article'] ?></div>
<div class='bInShop__eImage'><img src="<?php echo $item['photo'] ?>" alt="" width="160" height="160" title="" /></div>

<div class='bInShop__ePrice'><span class="price"><?php echo $item['price'] ?></span> <span class="rubl">p</span></div>
<div class='bInShop__eButton' ref="<?php echo $item['token'] ?>" data-value='<?php echo json_encode( $json ) ?>'>
  <?php include_component('cart', 'buy_button', array('product' => $product, 'quantity' => 1, 'view' => 'add')) ?>
</div>
<p class='bInShop__eDescription'><?php echo $item['description'] ?></p>
<div class='bInShop__eButton'><a href="<?php echo $item['url'] ?>" class='bWhiteButton'>Подробнее о товаре</a></div>