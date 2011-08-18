<form class="form order-form" data-update-url="<?php echo url_for('order_updateField', array('step' => 1)) ?>" action="<?php echo url_for('order_new', array('step' => 1)) ?>" method="post">
  <?php echo $form->renderHiddenFields() ?>

  <?php if (empty($form->getObject()->region_id)): ?>
  <ul>
    <?php echo $form['region_id']->renderRow() ?>
  </ul>
  <input type="submit" value="Подтвердить" />

  <?php else: ?>
  <ul>
    <li class="form-row">
      <?php echo $form['region_id']->renderLabel() ?>
      <br /><?php echo $form->getObject()->Region ?>
      <a href="#">изменить</a>
    </li>
    <?php foreach ($form as $name => $field): ?>
      <?php if ((isset($form[$name]) && $form[$name]->isHidden()) || (!isset($form[$name]) && $field->isReal())) continue ?>
      <?php echo $field->renderRow() ?>
    <?php endforeach ?>
  </ul>
  <input type="submit" value="Продолжить оформление" />

  <?php endif ?>
</form>