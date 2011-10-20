<?php foreach ($list as $item): ?>
  <a href="<?php echo $item['url'] ?>" style="font-size: <?php echo 8 == rand(1, 8) ? rand(22, 26) : rand(10, 21) ?>px;"><?php echo $item['name'] ?></a> &nbsp;
<?php endforeach ?>
