<?php
/**
 * @var $list ProductEntity[]
 */
?>
<?php $in_row = isset($in_row) ? $in_row : 3 ?>
<?php $last_line = isset($last_line) ? $last_line : true ?>
<div class="goodslist"<?php echo (4 == $in_row) ? ' style="width: 940px; float: none; margin: 0;"' : ''?>>
  <?php $i = 0; foreach ($list as $item): $i++; ?>
  <?php include_partial('show_', array('view' => 'compact', 'item' => $item)); ?>

  <?php if (!($i % $in_row) && (count($list) == $i ? $last_line : true)): ?>
    <div class="line"></div>
    <?php endif ?>

  <?php endforeach ?>
  <?php if (($i % $in_row) && $last_line): ?>
  <div class="line"></div>
  <?php endif ?>
</div>