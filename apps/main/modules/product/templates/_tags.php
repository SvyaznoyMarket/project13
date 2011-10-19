    <div class="pb25"><strong>Теги:</strong>
<?php $i = 0; foreach ($list as $tag): $i++; if ($i > 6) break; ?>
      <a href="<?php echo url_for('search', array('q' => $tag->name, )) ?>" class="underline" rel="nofollow"><?php echo $tag->name ?></a><?php echo $i == count($list) ? ', ' : '' ?>
<?php endforeach ?>

</div>