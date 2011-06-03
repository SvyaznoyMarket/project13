<ul>
<?php foreach ($list as $item): ?>
  <li>
    <strong><?php echo $item['has_link'] ? link_to($item['name'], 'productCard', $item['product']) : $item['name'] ?></strong>
    <br /><?php echo $item['creator'] ?>
    <?php include_component('product', 'property', array('product' => $item['product'])) ?>
  </li>
<?php endforeach ?>
</ul>