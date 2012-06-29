<?php slot('banner') ?>
<input id="main_banner-data" type="hidden" disabled="disabled" data-value='<?php echo str_replace("'", "&#39;", json_encode($promoData)) ?>' />
<?php end_slot() ?>