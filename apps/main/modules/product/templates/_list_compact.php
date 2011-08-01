<ul>
  <?php foreach ($list as $item): ?>
    <li>
        <?php include_component('product', 'show', array('view' => 'compact', 'product' => $item)) ?>
    </li>
  <?php endforeach ?>
</ul>
