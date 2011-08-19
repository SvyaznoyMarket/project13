<li data-field="address" class="form-row">
  <?php echo $form['address']->renderLabel() ?>
  <?php echo $form['address']->renderError() ?>
  <div class="content">
    <?php echo $form['address']->render() ?>

  <?php if (count($widget->getChoices()) > 0): ?>
    <?php echo $widget->render('order_user_address', null, array('class' => 'order_user_address')) ?>
  <?php endif ?>
  </div>
</li>
