<?php
/**
 * @var ServiceEntity $service
 */

slot('title', $service->getName());
slot('navigation');
  include_component('default', 'navigation', array('list' => $service->getNavigation()));
end_slot();
?>

<?php render_partial('service/templates/_show.php', array('service'=>$service))?>

<?php render_partial('service/templates/_alike_service.php', array('service' => $service)) ?>