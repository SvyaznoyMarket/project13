<table class="table">
  <tr>
    <th>Товар</th>
    <th>&nbsp;</th>
  </tr>
  <?php foreach ($list as $item): ?>
  <tr>
    <td><?php echo $item['name'] ?></td>
    <td><a href="<?php echo url_for('userDelayedProduct_delete', $item['product']) ?>">удалить</a></td>
  </tr>
  <?php endforeach ?>
</table>