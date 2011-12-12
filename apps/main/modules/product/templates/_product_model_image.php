<div class="bDropWrap">
  <h5><?php echo $property->name ?>:</h5>

  <ul class="previewlist">
  <?php foreach ($property->values as $value): ?>
      <li><b<?php echo ($value['is_selected']) ? ' class="current"' : '' ?> title="<?php echo $value['parameter']->getValue() ?>"><a href="<?php echo $value['url'] ?>"></a></b><img src="<?php echo $value['photo'] ?>" alt="<?php echo $value['parameter']->getValue() ?>" width="48" height="48" /></li>
  <?php endforeach ?>
  </ul>
</div>

<div class="clear"></div>
