<?php
/**
 * @var $item ProductEntity
 */
?>

<?php if($item->getModel() && $item->getModel()->getPropertyList()): ?>
  <!-- Variation -->
  <div class="fr width400">
    <h2>Этот товар с другими параметрами:</h2>
    <?php
    foreach ($item->getModel()->getPropertyList() as $property){
      $data = array(
        'item' => $item,
        'property'=>$property,
      );
      if($property->getIsImage())
        render_partial('product_/templates/_product_model_image.php', $data);
      else
        render_partial('product_/templates/_product_model_select.php', $data);
    }
    ?>
  </div>
  <!-- /Variation -->
<?php endif ?>
