<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\GridCell\Entity[] $gridCells
 * @param \Model\Product\CompactEntity[] $productsById
 */
$f = function(
    \Helper\TemplateHelper $helper,
    array $gridCells,
    array $productsById = []
) {
?>

<? foreach ($gridCells as $cell): ?>
<div>
    <? if (\Model\GridCell\Entity::TYPE_PRODUCT === $cell->getType()): ?>
        <? if ($product = (isset($productsById[$cell->getId()]) ? $productsById[$cell->getId()] : null)) continue ?>
        <?= $helper->render('product/show/__grid', ['product' => $product]) ?>
    <? elseif (\Model\GridCell\Entity::TYPE_IMAGE === $cell->getType()): ?>

    <? endif ?>
</div>
<? endforeach ?>

<?
}; return $f;