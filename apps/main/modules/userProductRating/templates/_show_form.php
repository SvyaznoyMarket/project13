<form action="<?php echo url_for('userProductRating_create', $sf_data->getRaw('product')) ?>" method="post">
<?php foreach ($list as $value): ?>
  <input id="product-<?php echo $product->token ?>-rating-<?php echo $value?>" type="radio" name="value" value="<?php echo $value ?>" <?php if ($userValue == $value) echo 'checked="checked"' ?> />
  <label for="product-<?php echo $product->token ?>-rating-<?php echo $value?>"><?php echo $value ?></label>
<?php endforeach ?>
  <input type="submit" value="Оценить" />
</form>