<?php
/**
 * @var $page         \View\Layout
 * @var $myThingsData array
 */
?>

<div id="myThingsTracker" class="jsanalytics" data-value="<?= $page->json($myThingsData) ?>"></div>