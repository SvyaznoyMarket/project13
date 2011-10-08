<?php slot('title', 'Оформление заказа - Шаг '.$step) ?>

<?php slot('navigation') ?>
  <?php include_component('order', 'navigation', array('order' => $order)) ?>
<?php end_slot() ?>

<div class="block">
  <?php include_component('order', 'step'.$step, array('form' => $form)) ?>
</div>
