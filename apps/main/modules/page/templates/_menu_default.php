<?php foreach ($list as $item): ?>
<h2><?php echo $item['name'] ?></h2>
<ul class="leftmenu pb20">
  <?php foreach ($item['links'] as $link): ?>
  <?php if ($page->token == $link['token']): ?>
  <li><strong class="orange"><?php echo $link['name'] ?></strong></li>
  <?php else: ?>
    <li><a href="<?php echo $link['url'] ?>"><?php echo $link['name'] ?></a></li>
  <?php endif ?>

  <?php endforeach ?>
</ul>
<?php endforeach ?>
