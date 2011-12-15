<?php slot('title', 'Оформление заказа') ?>

<?php slot('navigation') ?>
  <?php include_component('order', 'navigation', array('order' => $order)) ?>
<?php end_slot() ?>

<?php slot('user') ?>
  <?php include_partial('order/user') ?>
<?php end_slot() ?>

<?php slot('receipt') ?>
  <?php include_component('order', 'receipt') ?>
<?php end_slot() ?>


<form id="region" method="post" action="/"></form>
<form class="form order-form" data-update-field-url="<?php echo url_for('order_updateField', array('step' => 1)) ?>" action="<?php echo url_for('order_new', array('step' => 1)) ?>" method="post" style="width: 665px;">
  <?php echo $form->renderHiddenFields() ?>

  <div class="fl width215 mr20"><strong class="font16">Способ получения заказа:</strong></div>
    <div class="fl width430">

  <?php if (empty($form->getObject()->region_id)): ?>
    <?php include_component('order', 'field_region_id', array('form' => $form)) ?>
      <input type="submit" value="Подтвердить" />

  <?php else: ?>
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


<?php slot('seo_counters_advance') ?>
  <?php include_component('order', 'seo_counters_advance', array('step' => 2)) ?>
<?php end_slot() ?>