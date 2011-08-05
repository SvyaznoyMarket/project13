<ul class="inline">
<?php foreach ($list as $item): ?>
  <li<?php if ($item['current']) echo ' class="current"' ?>><a href="<?php echo $item['url'] ?>"><?php echo $item['title'] ?></a></li>
<?php endforeach ?>
</ul>