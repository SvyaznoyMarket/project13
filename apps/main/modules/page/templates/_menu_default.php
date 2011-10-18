<?php foreach ($list as $item): ?>
<div class="font16 orange pb10"><?php echo $item['name'] ?></div>
<ul class="leftmenu pb10">
  <?php foreach ($item['links'] as $link): ?>
  <?php if ($page->token == $link['token']): ?>
  <li><strong class="orange"><?php echo $item['name'] ?></strong></li>
  <?php else: ?>
    <li><a href="<?php echo $link['url'] ?>"><?php echo $link['name'] ?></a></li>
  <?php endif ?>

  <?php endforeach ?>
</ul>
<?php endforeach ?>
