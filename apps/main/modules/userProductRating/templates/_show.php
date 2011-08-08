<div class="block product_rating_<?php echo $product->id ?>-block">

  <div class="left block-inline">
    Рейтинг: <?php echo round($product->rating, 1) ?>
  </div>

  <?php if ($sf_user->isAuthenticated()): ?>
  <div class="left">
    <?php include_component('userProductRating', 'form', array('product' => $product)) ?>
  </div>
  <?php endif ?>

  <br class="clear" />

</div>