<table class="table">
  <tr>
    <th>Товар</th>
    <th>Количество</th>
    <th>&nbsp;</th>
  </tr>
  <?php foreach ($list as $item): ?>
  <tr>
    <td><?php echo $item['name'] ?></td>
    <td><?php echo $item['quantity'] ?></td>
    <td><?php echo link_to('удалить', 'cart_delete', array('product' => $item['token']), array('class' => 'cart cart-delete')) ?></td>
  </tr>
  <?php endforeach ?>
</table>