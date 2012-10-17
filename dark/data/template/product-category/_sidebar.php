<?php
/**
 * @var $page          \View\Layout
 * @var $category      \Model\Product\Category\Entity
 * @var $productFilter \Model\Product\Filter
 */
?>

<? require __DIR__ . '/_branch.php' ?>
<? if (isset($productFilter)) require __DIR__ . '/_filter.php' ?>