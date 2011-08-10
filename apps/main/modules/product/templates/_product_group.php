<?php foreach ($properties as $property): ?>
  <?php echo $property->name ?>:&nbsp;
  <?php foreach ($property->values as $value): ?>
    <?php echo link_to($value['name'], "changeProduct", array_merge($sf_data->getRaw('product')->toParams(), array('property' => $property->id, 'value' => $value['value'], ) ), array('class' => (isset($value['is_selected']) && true === $value['is_selected']) ? 'strong' : '', )) ?>&nbsp;
    <?php //echo link_to($value->value, "changeProduct", $sf_data->getRaw('product')) ?>&nbsp;
  <?php endforeach ;?>
  <br/>
<?php endforeach; ?>
