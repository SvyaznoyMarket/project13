<form class="form order-form" data-update-url="<?php echo url_for('order_updateField', array('step' => 2)) ?>" action="<?php echo url_for('order_new', array('step' => 2)) ?>" method="post">
  <?php echo $form->renderHiddenFields() ?>

  <ul>
    <?php echo $form ?>
  </ul>
  <input type="submit" value="Продолжить оформление" />

</form>