<?php
/**
 * @var $page          \View\Layout
 * @var $category      \Model\Product\Category\Entity
 * @var $productFilter \Model\Product\Filter
 */
?>

<? require __DIR__ . '/_branch.php' ?>
<? if (isset($productFilter)) require __DIR__ . '/_filter.php' ?>

<? if (\App::config()->adFox['enabled']): ?>
<div class="pb15">
    <div class="adfoxWrapper" id="adfox215"></div>
</div>
<? endif ?>