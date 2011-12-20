<?php
#JSON data
	$json = array (
		'jsref' => $item['product']->token,
		'jsimg' => $item['photo'],
		'jstitle' => $item['name'],
		'jsprice' => $item['product']->getFormattedPrice()
	)
?>
    <h2 class="mbSet"><strong><?php echo $item['name'] ?></strong></h2>
    <div class="line pb15"></div>

	<div class='bSet' data-value='<?php echo json_encode( $json ) ?>'>
		<div class='bSet__eImage'>
			<a href="<?php echo $item['url'] ?>" title="<?php echo $item['name'] ?>"><img src="<?php echo $item['photo'] ?>" alt="<?php echo $item['name'] ?>" width="500" height="500" title="" /></a>
		</div>
		<div class='bSet__eInfo'>
			<div class='bSet__eArticul'>Артикул #<?php echo $item['product']->article ?></div>
			<p class='bSet__eDescription'><?php echo $item['description'] ?></p>
			<div class='bSet__ePrice'>
				<?php include_partial('product/price', array('price' => $item['product']->getFormattedPrice(), )) ?>
        <?php include_component('cart', 'buy_button', array('product' => $item['product'], 'quantity' => 1, 'value' => array('Купить набор'),)) ?>

        <?php if ($item['product']->is_insale): ?>
        <div class="pb5"><strong><a href="<?php echo url_for('order_1click', array('product' => $item['barcode'])) ?>" class="red underline order1click-link">Купить быстро в 1 клик</a></strong></div>
        <?php endif ?>

        <?php if ($item['product']->is_insale): ?>
				<div class="pb5"><strong class="orange">Есть в наличии</strong></div>
        <?php endif ?>
			</div>
			<div class='bSet__eIconsWrap'>
        <?php if (count($item['part'])): ?>
				<h3 class='bSet__eG'>Состав набора:</h3>
				<div class='bSet__eIcons'>
					<ul class="previewlist">
                                            <?php foreach ($item['part'] as $part): ?>
        			            <li><b><a href="<?php echo $part['url'] ?>" title="<?php echo $part['name'] ?>"></a></b><img src="<?php echo $part['photo'] ?>" alt="<?php echo $part['name'] ?>" width="48" height="48"></li>
                                            <?php endforeach ?>
			        </ul>
				</div>
        <?php endif ?>
				<div class='bSet__eTWrap'><a class='bSet__eMoreInfo' href="<?php echo $item['url'] ?>">Подробнее о наборе</a></div>
			</div>
		</div>
	</div>
