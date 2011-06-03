<?php  if ($cart->count() > 0): ?>
<ul>
<?php foreach ($cart->getProducts() as $product): ?>
      <li><?php echo $product['name']." x".$product['cart']['amount']." ".link_to('удалить', '@default?module=cart&action=delete&product='.$product['token']) ?></li>
<?php endforeach ?>
</ul>
<?php echo link_to('очистить', '@default?module=cart&action=clear') ?>
<?php  else: ?>
пусто. совсем.
<?php endif ?>