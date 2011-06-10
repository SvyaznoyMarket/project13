<ul class="menu">
<?php foreach ($list as $item): ?>
  <li<?php if ($item['current']) echo ' class="current"' ?>><a href="<?php echo url_for($item['url']) ?>"><?php echo $item['name'] ?></a></li>
<?php endforeach ?>
</ul>