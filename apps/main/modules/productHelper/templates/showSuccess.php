<h1><?php echo $productHelper->name ?></h1>

<div class="block">
  <?php include_component('productHelper', 'filter', array('productHelper' => $productHelper)) ?>
</div>