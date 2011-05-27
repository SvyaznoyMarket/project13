<ul>
<?php foreach ($list as $item): ?>
  <li>
    <strong><?php echo $item['name'] ?></strong>: <?php echo $item['value'] ?>;
  </li>
<?php endforeach ?>
</ul>
