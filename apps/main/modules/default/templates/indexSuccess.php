<ul>
<?php foreach ($productList as $product): ?>
  <li>
    <h2><?php echo $product->name ?></h2>
    <?php foreach ($product->Property as $property): ?>
      <strong><?php echo $property->name ?></strong>: <?php echo $property->Value ?>;
    <?php endforeach ?>
  </li>
<?php endforeach ?>
</ul>
