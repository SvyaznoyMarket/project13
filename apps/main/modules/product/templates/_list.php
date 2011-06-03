<ul>
<?php foreach ($list as $item): ?>
  <li>
    <strong><?php echo $item['has_link'] ? link_to($item['name'], 'productCard', $item['product']) : $item['name'] ?></strong>
  <string><?php include_component('cart', 'buy_button', array('token' => $item['product']['token'], 'amount' => 1, )) ?></string>

    <?php include_component('product', 'property', array('product' => $item['product'])) ?>
  </li>
<?php endforeach ?>
</ul>