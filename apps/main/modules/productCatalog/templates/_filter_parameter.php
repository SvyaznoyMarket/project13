<ul>
<?php foreach ($list as $item): ?>
  <li>
    <strong><?php echo $item['name'] ?></strong><br />
    <?php include_partial('productCatalog/filter_parameter_'.$item['type'], array('item' => $item)) ?>
  </li>
<?php endforeach ?>
</ul>