<?php if (false): ?>
  <ul id="open_auth-block">
    <?php foreach ($list as $item): ?>
      <li><a id="open_auth_<?php echo $item['token'] ?>-link" class="open_auth-link" href="<?php echo $item['url'] ?>" target="_blank"><?php //echo $item['name']  ?></a></li>
    <?php endforeach ?>
  </ul>
<?php endif ?>

<ul class="backetsharelist">
<?php foreach ($list as $item): ?>
  <li>
    <a id="open_auth_<?php echo $item['token'] ?>-link" class="open_auth-link <?php echo $item['token'] ?>" href="<?php echo $item['url'] ?>" target="_blank"><?php echo $item['name']  ?></a>
  </li>
<?php endforeach ?>
</ul>