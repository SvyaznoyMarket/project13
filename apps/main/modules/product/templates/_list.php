<?php if (!isset($list[0])): ?>
  <p>нет товаров</p>

<?php else: ?>
<ul>
  <?php foreach ($list as $item): ?>
    <li>
      <strong><?php echo $item['has_link'] ? link_to($item['name'], 'productCard', $item['product']) : $item['name'] ?></strong>
      <?php echo $item['creator'] ?>

      <?php include_partial('product/price', array('price' => $item['price'])) ?>

      <ul class="inline">
        <li><?php include_component('cart', 'buy_button', array('product' => $item['product'], 'quantity' => 1, )) ?></li>
      </ul>

      <?php include_component('product', 'property', array('product' => $item['product'])) ?>
    </li>
  <?php endforeach ?>
</ul>
<?php endif ?>