<!-- Variation -->
<div class="fr width400">
<<<<<<< HEAD
  <h2>Этот товар с другими параметрами:</h2>
=======
  <h2>Другие вариации:</h2>
>>>>>>> e3d3e88... Changed template of product's model
  <?php foreach ($properties as $property): ?>
    <?php include_partial('product/product_model_'.($property->ProductModelRelation[0]->is_image ? 'image' : 'select'), array('product' => $product, 'property' => $property, )) ?>
  <?php endforeach; ?>
</div>
<!-- /Variation -->
