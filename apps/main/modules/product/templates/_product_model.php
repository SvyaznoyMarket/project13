    <!-- Variation -->
    <div class="fr width400">
      <h2>Другие вариации:</h2>
      <?php foreach ($properties as $property): ?>
        <?php include_partial('product/product_model_'.($property->ProductModelRelation[0]->is_image ? 'image' : 'select'), array('product' => $product, 'property' => $property, )) ?>
      <?php endforeach; ?>
    </div>
    <!-- /Variation -->
