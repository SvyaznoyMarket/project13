<h1><?php echo $step ?>-й шаг оформления заказа</h1>

<div class="block">
  <?php include_component('order', 'navigation', array('order' => $order)) ?>
</div>

<div class="block">
  <?php include_component('order', 'step'.$step, array('form' => $form)) ?>
</div>
