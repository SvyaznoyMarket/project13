<?php
/**
 * @var $price
 * @var $noStrong
 */
?>
<?php if (!isset($noStrong) || false == $noStrong): ?><strong <?php echo (isset($noClasses) && $noClasses) ? '' : 'class="font34"' ?>><?php endif ?>
  <span class="price"><?php echo $price ?></span> <span
  class="rubl">p</span><?php if (!isset($noStrong) || false == $noStrong): ?></strong><?php endif ?>