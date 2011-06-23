<div class="left block-inline">
  Рейтинг: <?php echo round($product->rating, 1) ?>
</div>

<?php if ($sf_user->isAuthenticated()): ?>
<div class="left">
  <?php include_partial('userProductRating/show_form', $sf_data) ?>
</div>
<?php endif ?>

<br class="clear" />