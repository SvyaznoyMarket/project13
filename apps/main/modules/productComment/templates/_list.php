<ul>
<?php foreach ($list as $item): ?>
  <li>
    <strong><?php echo $item['date'] ?></strong> от <?php echo $item['author'] ?><br />

    <?php echo $item['content'] ?>
  </li>
<?php endforeach ?>
</ul>
