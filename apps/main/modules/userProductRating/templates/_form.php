<form class="product_rating-form event-submit" data-event="form.ajax-submit" data-target=".product_<?php echo $item->id ?>_rating-block" action="<?php echo url_for('userProductRating_create', $sf_data->getRaw('product')) ?>" method="post">

  <table class="table">
  <?php foreach ($list as $item): ?>
    <tr>
      <td><?php echo $item['name'] ?></td>
      <td>
      <?php foreach ($item['ratings'] as $rating): ?>
        <input id="<?php echo $rating['id'] ?>" type="radio" name="rating[<?php echo $rating['property_id'] ?>]" value="<?php echo $rating['value'] ?>" <?php if ($rating['selected']) echo 'checked="checked"' ?> />
          <label for="<?php echo $rating['id'] ?>"><?php echo $rating['value'] ?></label>
      <?php endforeach ?>
      </td>
    </tr>
  <?php endforeach ?>
  </table>

  <input type="submit" value="Оценить" />
</form>
