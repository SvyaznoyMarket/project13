<?php
/**
 * @var $page          \View\Layout
 * @var $category      \Model\Product\Category\Entity
 */
?>
<?php
if (!$category || !$page) return;
?>
<div id="_categoryData" style="display: none;" data-category='<?=$page->json(\Kissmetrics\Manager::getCategoryEvent($category))?>'></div>