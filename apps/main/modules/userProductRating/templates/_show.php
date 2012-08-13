<?php if (false): ?>
<div class="block product_<?php echo $item->id ?>_rating-block">

  <div class="left block-inline">
    Рейтинг: <?php echo round($item->rating, 1) ?>
  </div>

  <?php if ($sf_user->isAuthenticated()): ?>
  <div class="left">
    <?php include_component('userProductRating', 'form', array('product' => $item)) ?>
  </div>
  <?php endif ?>

  <br class="clear" />

</div>
<?php endif ?>
Оценка пользователей: <a href="#anchor1" class="nodecor"><img src="/images/stars2.png" alt="" width="83" height="16" class="vm ml5" /> <strong class="ml5"><?php echo round($item->rating, 1) ?></strong></a>