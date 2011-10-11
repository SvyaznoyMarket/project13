<?php if (false): ?>
<ul class="inline">
<?php foreach ($list as $item): ?>
  <li class="block"><a href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a></li>
<?php endforeach ?>
</ul>
<?php endif ?>

<ul class="topmenu">
<?php $i = 0; foreach ($list as $item): $i++; ?>
    <li><a href="<?php echo $item['url'] ?>" class="point<?php echo $i % 9 ?>"><?php echo $item['name'] ?></a></li>
<?php endforeach ?>
</ul>
