<?php if (count($list)): ?>
<ul>
  <?php foreach($list as $service): ?>
  <li><?php echo link_to($service->name, "service_show", $service)." - ".$service->Price->getFirst()->price ?></li>
  <?php endforeach; ?>
</ul>
<?php endif; ?>