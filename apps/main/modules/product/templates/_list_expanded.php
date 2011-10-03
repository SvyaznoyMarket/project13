<?php if (false): ?>
<ul>
  <?php foreach ($list as $item): ?>
    <li>
      <?php include_component('product', 'show', array('view' => 'expanded', 'product' => $item)) ?>
    </li>
  <?php endforeach ?>
</ul>
<?php endif ?>
<?php foreach ($list as $item): ?>
  <?php include_component('product', 'show', array('view' => 'expanded', 'product' => $item)) ?>
<?php endforeach ?>
