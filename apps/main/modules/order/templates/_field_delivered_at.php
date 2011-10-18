  <div class="delivery_block">
  <div class="pb10">
    <?php echo $form['delivered_at']->renderLabel() ?>
    <?php echo $form['delivered_at']->renderError() ?>
    <?php //echo $form['delivered_period_id']->renderError() ?>
  </div>
  <div class="selectbox selectbox225 fl mr10">
    <i></i>
    <!--span id="selectorder_delivered_at" class="select">15.11.11 - понедельник</span-->
    <?php echo $form['delivered_at']->render(array('class' => 'styled', )) ?>
  </div>
  </div>