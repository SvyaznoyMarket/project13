<?php
slot('title',$service);
slot('navigation');
  include_component('service', 'navigation', array('service' => $service));
end_slot();
?>

<?php include_component('service', 'show', array('service' => $service, 'showNoPrice' =>$showNoPrice)) ?>

<?php include_component('service', 'alike_service', array('service' => $service,  'showNoPrice' =>$showNoPrice)) ?>