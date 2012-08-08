<?php
    #slot('navigation', get_component('default', 'navigation', array('sf_data' => $sf_data, 'list' => $breadCrumbElementList)));
    slot('title', $wpTitle);
?>

<?php echo $sf_data->getRaw('wpContent');?>