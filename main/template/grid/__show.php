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
    $step = 60;
    $offset = 20;
    $contentHeight = 0;
?>

<? foreach ($gridCells as $cell): ?>
<div style="position: absolute; background: #edc195; overflow: hidden; border: dashed 1px #ed560e;
    <?= 'left: ' . (($cell->getColumn() - 1) *  $step + ($cell->getColumn() - 1) * $offset) . 'px;' ?>
    <?= 'top: ' . (($cell->getRow() - 1) *  $step + ($cell->getRow() - 1) * $offset) . 'px;' ?>
    <?= 'width:' . ($cell->getSizeX() * $step + ($cell->getSizeX() - 1) * $offset) . 'px;' ?>
    <?= 'height:' . ($cell->getSizeY() * $step + ($cell->getSizeY() - 1) * $offset) . 'px;' ?>
">
    <? if (\Model\GridCell\Entity::TYPE_PRODUCT === $cell->getType()): ?>
    <?
        $product = (isset($productsById[$cell->getId()]) ? $productsById[$cell->getId()] : null);
        if (!$product) continue;
    ?>
        <?= $helper->render('product/show/__grid', ['product' => $product]) ?>
    <? elseif (\Model\GridCell\Entity::TYPE_IMAGE === $cell->getType()): ?>
    <?
        $content = $cell->getContent();
        $url = !empty($content['url']) ? $content['url'] : null;
    ?>
        <img src="<?= $url ?>" />
    <? endif ?>
</div>
<? endforeach ?>

<?
}; return $f;