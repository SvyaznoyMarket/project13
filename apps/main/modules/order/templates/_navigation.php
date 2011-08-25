<ul class="inline">
  <?php foreach ($list as $item): ?>
    <li>
      <?php if ($item['is_active']): ?>
        <a href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a>
      <?php else: ?>
        <?php echo $item['name'] ?>
      <?php endif ?>
    </li>
  <?php endforeach ?>
</ul>