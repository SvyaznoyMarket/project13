<?php $in_row = isset($in_row) ? $in_row : 3 ?>
<div class="goodslist"<?php echo (4 == $in_row) ? ' style="width: 940px; float: none; margin: 0;"' : ''?>>
<?php $i = 0; foreach ($list as $item): $i++; ?>
  <?php include_component('product', 'show', array('view' => 'compact', 'product' => $item)) ?>

  <?php if (!($i % $in_row)): ?>
    <div class="line"></div>
  <?php endif ?>

<?php endforeach ?>
  <?php if ($i % $in_row): ?>
    <div class="line"></div>
  <?php endif ?>
</div>