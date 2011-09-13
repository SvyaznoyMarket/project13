<table class="table">
  <tr>
    <th>Товар</th>
    <th>Количество</th>
    <th>Услуги F1</th>
    <th>&nbsp;</th>
  </tr>
  <?php foreach ($list as $item): ?>
  <tr>
    <td><?php echo $item['name'] ?></td>
    <td><?php echo $item['quantity'] ?></td>
    <td>
      <?php foreach ($item['service'] as $service): ?>
      <?php echo "[".$service['quantity']."] ".$service['name']." [".link_to('добавить', 'cart_service_add', array('product' => $item['token'], 'service' => $service['token'], 'quantity' => 1, ))."]"." [".link_to('удалить', 'cart_service_delete', array('product' => $item['token'], 'service' => $service['token'], ))."]" ?><br />
      <?php endforeach; ?>
    </td>
    <td><?php echo link_to('удалить', 'cart_delete', array('product' => $item['token']), array('class' => 'cart cart-delete')) ?></td>
  </tr>
  <?php endforeach ?>
</table>