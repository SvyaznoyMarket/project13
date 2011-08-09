<form class="event-submit product_comment-form" data-event="form.ajax-submit" data-list-target=".product_<?php echo $product->id ?>_comment-block" action="<?php echo url_for(array('sf_route' => 'productComment_create', 'sf_subject' => $sf_data->getRaw('product'), 'parent' => $parent ? $parent->id : null)) ?>" method="post">
  <ul class="form">
    <?php echo $form ?>
  </ul>

  <input type="submit" value="Оставить комментарий" />
</form>