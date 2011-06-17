<table class="table">

  <tr>
  <?php $i = 0; foreach ($list as $item): $i++ ?>
    <?php if (0 == $i): ?><th>Характеристика</th><?php endif ?>
    <th><?php echo $item['name'] ?></th>
  <?php endforeach ?>
  </tr>
</table>