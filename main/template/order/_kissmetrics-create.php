<?php
/**
 * @var $page \View\Layout
 */
?>

<div id="kissmetrics" data-value="<?= $page->json((new \Kissmetrics\Manager())->getOrderNewEvent()) ?>"></div>