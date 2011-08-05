<table class="table">
<<<<<<< HEAD
  <tr>
    <th>Товар</th>
    <th>Цена</th>
    <th>Количество</th>
  </tr>
<?php foreach ($list as $item): ?>
  <tr>
    <td><a href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a></td>
    <td><span class="price"><?php echo $item['price'] ?></span></td>
    <td><?php echo $item['quantity'] ?></td>
  </tr>
<?php endforeach ?>
</table>