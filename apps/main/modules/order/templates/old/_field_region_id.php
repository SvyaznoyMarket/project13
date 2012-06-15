<?php /* ?>
<li data-field="region_id" class="form-row">
  <?php echo $form['region_id']->renderLabel() ?>
  <?php echo $form['region_id']->renderError() ?>
  <div class="content">
    <?php echo $widget->render('order_region', (string)$region) ?>
  </div>
</li>
<?php */ ?>

<div class="pb10">
  <?php echo $form['region_id']->renderLabel() ?>
  <?php echo $form['region_id']->renderError() ?>
</div>
<div data-field="region_id" class="selectbox selectbox429 mb10"><i></i>
  <?php echo $form['region_id']->render(array('class' => 'styled' )) ?>
</div>