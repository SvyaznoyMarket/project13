<table class="table">
  <tr>
    <th>Название</th>
    <th>&nbsp;</th>
  </tr>
  <?php foreach ($list as $item): ?>
  <tr>
    <td><?php echo $item['name'] ?></td>
    <td><?php echo link_to('редактировать', 'userAddress_edit', $item['userAddress']) ?></td>
    <td><?php echo link_to('удалить', 'userAddress_delete', $item['userAddress']) ?></td>
  </tr>
  <?php endforeach ?>
</table>