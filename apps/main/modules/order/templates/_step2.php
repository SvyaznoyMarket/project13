<?php slot('receipt') ?>
  <?php include_component('order', 'receipt') ?>
<?php end_slot() ?>

<?php slot('step') ?>
        <ul class="steplist steplist2">
            <li><a href="<?php echo url_for('order_new', array('step' => 1,)) ?>"><span>Шаг 1</span>Данные<br />покупателя</a></li>
            <li><a href="<?php echo url_for('order_new', array('step' => 1,)) ?>"><span>Шаг 2</span>Способ доставки<br />и оплаты</a></li>
            <li><span>Шаг 3</span>Подтверждение<br />заказа</li>
        </ul>
<?php end_slot() ?>

<form class="form order-form" data-update-field-url="<?php echo url_for('order_updateField', array('step' => 1)) ?>" action="<?php echo url_for('order_new', array('step' => 1)) ?>" method="post">
  <?php echo $form->renderHiddenFields() ?>

  <div class="fl width215 mr20"><strong class="font16">Способ получения заказа:</strong></div>
    <div class="fl width430">
      <div class="pb10">В каком городе вы будете получать заказ?</div>

  <?php if (empty($form->getObject()->region_id)): ?>
    <?php include_component('order', 'field_region_id', array('form' => $form)) ?>
      <input type="submit" value="Подтвердить" />

  <?php else: ?>
      <?php //echo $form['region_id']->renderLabel() ?>
        <div class="pb15"><strong class="mr10"><?php echo $form->getObject()->Region ?></strong> <a class="button whitelink" href="#">Изменить</a></div>
    <?php foreach ($form as $name => $field): ?>
      <?php if ((isset($form[$name]) && $form[$name]->isHidden()) || (!isset($form[$name]) && $field->isReal())) continue ?>
      <?php if (sfContext::getInstance()->getController()->componentExists('order', 'field_'.$name)): ?>
        <?php include_component('order', 'field_'.$name, array('form' => $form)) ?>
      <?php else: ?>
        <?php echo $form[$name]->renderRow(); ?>
      <?php endif ?>

    <?php endforeach ?>
    </div>
        <div class="line pb20"></div>
        <div class="pl235"><input type="submit" class="button bigbutton" id="bigbutton" value="Продолжить оформление" /></div>

  <?php endif ?>
</form>