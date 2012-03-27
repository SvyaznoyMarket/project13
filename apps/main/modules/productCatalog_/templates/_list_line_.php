<?php
/**
 * @var $list ProductEntity[]
 */
?>
<div class="goodslist">
  <?php $i = 0; foreach ($list as $item): $i++; ?>
  <?php include_partial('show_', array('view' => 'line', 'item' => $item)) ?>

  <?php if (!($i % 3)): ?>
    <div class="line"></div>
    <?php endif ?>

  <?php endforeach ?>
</div>