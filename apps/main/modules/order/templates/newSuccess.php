<?php slot('title', 'Оформление заказа - Шаг 2'/*.$step*/) ?>

<?php slot('navigation') ?>
  <?php include_component('order', 'navigation', array('order' => $order)) ?>
<?php end_slot() ?>

  <?php include_component('order', 'step'.$step, array('form' => $form)) ?>