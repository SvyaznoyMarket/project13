<ul>
  <?php foreach ($list as $item): ?>
  <li><?php echo $item['name'] ?> <a href="<?php echo url_for('userDelayedProduct_delete', $item['product']) ?>">удалить</a></li>
  <?php endforeach ?>
</ul>