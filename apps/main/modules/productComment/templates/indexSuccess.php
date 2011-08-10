<div class="block">
  <?php include_component('productCard', 'navigation', array('product' => $product)) ?>
</div>

<div class="block">
  <?php include_partial('product/name', array('product' => $product)) ?>
</div>

<div class="block">
  <?php include_component('productComment', 'form', array('product' => $product, 'parent' => $parent, 'form' => $form)) ?>
</div>

<div class="block">
  <?php include_component('productComment', 'list', array('product' => $product)) ?>
</div>
