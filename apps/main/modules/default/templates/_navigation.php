<?php if (false): ?>
<ul class="navigation">
<?php $i = 0; $count = count($list); foreach ($list as $i => $item): $i++ ?>
  <li>
    <a href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a><?php if ($i < $count) echo ' / ' ?>
  </li>
<?php endforeach ?>
</ul>
<?php endif; ?>
<?php $i = 0; $count = count($list); foreach ($list as $i => $item): $i++ ?>
    <a href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a><?php if ($i < $count) echo ' / ' ?>
<?php endforeach ?>
