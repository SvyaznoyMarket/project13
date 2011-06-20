<table class="table">

  <tr>
    <th>Характеристика</th>
  <?php foreach ($productList as $product): ?>
    <th><?php echo $product ?> <a href="<?php echo url_for('userProductCompare_delete', $product) ?>">удалить</a></th>
  <?php endforeach ?>
  </tr>

  <?php foreach ($list as $item): ?>
  <tr>
    <?php if ('group' == $item['type']): ?>
      <td colspan="<?php echo ($productCount + 1) ?>" class="gray text-white"><?php echo $item['name'] ?></td>

    <?php else: ?>
      <td><?php echo $item['name'] ?></td>
      <?php foreach ($item['values'] as $value): ?>
        <td><?php echo $value ?></td>
      <?php endforeach ?>

    <?php endif ?>
  </tr>
  <?php endforeach ?>

</table>