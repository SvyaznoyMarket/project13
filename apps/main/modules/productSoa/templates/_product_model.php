<!-- Variation -->
<div class="fr width400">
  <h2>Этот товар с другими параметрами:</h2>
  <?php foreach ($properties as $property): ?>
    <?php include_partial('productSoa/product_model_'.($property->ProductModelRelation[0]->is_image ? 'image' : 'select'), array('product' => $product, 'property' => $property, )) ?>
  <?php endforeach; ?>
</div>
<!-- /Variation -->
