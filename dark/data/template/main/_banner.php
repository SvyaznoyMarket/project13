<?php
/**
 * @var $page       \View\Layout
 * @var $bannerData array
 */
?>

<input id="main_banner-data" type="hidden" disabled="disabled" data-value="<?= $page->escape(json_encode($bannerData)) ?>" />
