<table class="table">
  <tr>
    <th>Название</th>
    <th>&nbsp;</th>
  </tr>
  <?php foreach ($list as $item): ?>
  <tr>
    <td><?php echo $item['name'] ?></td>
    <td><?php echo link_to('удалить', 'userTag_delete', $item['userTag']) ?></td>
  </tr>
  <?php endforeach ?>
</table>