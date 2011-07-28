<table class="table">
<?php foreach ($list as $item): ?>
  <tr>
    <td><a href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a></td>
    <td><span class="price"><?php echo $item['price'] ?></span></td>
  </tr>
<?php endforeach ?>
</table>