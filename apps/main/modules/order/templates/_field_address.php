    <div class="delivery_block js_address dynamic">
    		<div class="pb15">
              <?php echo $form['address']->renderLabel() ?>
              <?php echo $form['address']->renderError() ?>
            </div>
    <?php echo $form['address']->render(array('class' => 'text width418 mb5', )) ?>

  <?php if (count($widget->getChoices()) > 1): ?>
    <?php echo $widget->render('order_user_address', null, array('class' => 'order_user_address')) ?>
  <?php endif ?>
  </div>