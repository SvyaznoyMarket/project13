<ul>
<?php foreach ($list as $item): ?>
  <li>
    <strong><?php echo $item['has_link'] ? link_to($item['name'], 'productCard', $item['product']) : $item['name'] ?></strong>
    <?php include_component('cart', 'buy_button', array('product' => $item['product'], 'amount' => 1, )) ?>

    <?php include_component('product', 'property', array('product' => $item['product'])) ?>
  </li>
<?php endforeach ?>
</ul>