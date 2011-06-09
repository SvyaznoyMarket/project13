<ul>
<?php foreach ($list as $item): ?>
  <li style="margin-left: <?php echo ($item['level'] * 40) ?>px">
    <strong><?php echo $item['date'] ?></strong> от <?php echo $item['author'] ?> <a href="<?php echo $item['answer_url'] ?>">ответить</a><br />

    <?php echo $item['content'] ?>
  </li>
<?php endforeach ?>
</ul>
