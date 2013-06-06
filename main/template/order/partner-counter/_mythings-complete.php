<?php
/**
 * @var $page   \View\Order\CreatePage
 * @var $user   \Session\User
 * @var $orders \Model\Order\Entity[]
 * @var $productsById  \Model\Product\Entity[]
 */
?>

<div id="myThingsTracker" data-value="<?= $page->json(\Partner\Counter\MyThings::getCompleteOrder($orders, $productsById)) ?>" class="jsanalytics"></div>