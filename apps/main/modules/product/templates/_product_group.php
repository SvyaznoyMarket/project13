<?php //echo myDebug::dump($group->getProperty()->getFirst()) ?>
<?php foreach ($properties as $property): ?>
  <?php echo $property->name ?>:&nbsp;
  <?php foreach ($property->values as $value): ?>
    <?php echo link_to($value->value, "homepage") ?>&nbsp;
  <?php endforeach ;?>
  <br/>
<?php endforeach; ?>
