<ul>
<?php foreach ($list as $item): ?>
  <li>
    <strong><a href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a></strong>
  </li>
<?php endforeach ?>
</ul>