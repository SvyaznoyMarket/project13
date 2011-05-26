<ul style="list-style: decimal">
<?php foreach ($productList as $product): ?>
  <li>
    <h2><?php echo $product['name'] ?></h2>
    <?php foreach ($product['Parameter'] as $parameter): ?>
      <strong><?php echo $parameter->getName() ?></strong>: <?php echo $parameter->getValue() ?>;
    <?php endforeach ?>
  </li>
<?php endforeach ?>
</ul>
