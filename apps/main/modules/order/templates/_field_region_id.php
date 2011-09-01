<li data-field="region_id" class="form-row">
  <?php echo $form['region_id']->renderLabel() ?>
  <?php echo $form['region_id']->renderError() ?>
  <div class="content">
    <?php echo $widget->render('region_name', (string)$region) ?>
  </div>
</li>
