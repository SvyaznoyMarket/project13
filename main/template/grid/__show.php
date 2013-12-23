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
<div class="<?= 'mCol' . $cell->getColumn() ?> <?= 'mRow' . $cell->getRow() ?> <?= 'mSizeX' . $cell->getSizeX() ?> <?= 'mSizeY' . $cell->getSizeY() ?>">
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