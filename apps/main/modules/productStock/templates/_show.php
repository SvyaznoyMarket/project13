<table class="table">
  <?php foreach ($list as $item): ?>
  <tr>
    <td><a href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a></td>
    <td><?php echo $item['quantity'] ?></td>
  </tr>
  <?php endforeach ?>
</table>