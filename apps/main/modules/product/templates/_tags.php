<noindex>
    <div class="pb25">

      <strong>Теги:</strong>
      <?php $i = 0; foreach ($list as $item): $i++; if ($i > $limit) break; ?>
        <a href="<?php echo $item['url'] ?>" class="underline" rel="nofollow"><?php echo $item['name'] ?></a><?php echo $i < $limit ? ', ' : '' ?>
      <?php endforeach ?>

    </div>
</noindex>    