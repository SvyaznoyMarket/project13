<?php foreach ($list as $item): ?>
  <a href="<?php echo $item['url'] ?>" class="<?php echo $item['class'] ?>"><?php echo $item['name'] ?></a>
<?php endforeach ?>