<form action="<?php echo url_for(array('sf_route' => 'productComment_create', 'sf_subject' => $sf_data->getRaw('product'), 'parent' => $parent ? $parent->id : null)) ?>" method="post">
  <?php echo $form->renderHiddenFields() ?>
  <ul class="form">
    <?php echo $form ?>
  </ul>

  <input type="submit" value="Оставить комментарий" />
</form>