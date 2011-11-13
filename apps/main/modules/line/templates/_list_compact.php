<div class="goodslist" style="width: 940px; float: none; margin: 0;">
<?php $i = 0; foreach ($list as $item): $i++; ?>
  <?php include_component('product', 'show', array('view' => 'compact', 'product' => $item)) ?>

  <?php if (!($i % 4)): ?>
    <div class="line"></div>
  <?php endif ?>

<?php endforeach ?>
</div>