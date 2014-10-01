<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\GridCell\Entity[] $gridCells
 * @param \Model\Product\Entity[] $productsByUi
 */
$f = function(
    \Helper\TemplateHelper $helper,
    array $gridCells,
    array $productsByUi = []
) {
    $config = \App::config()->tchibo;

    $step = $config['rowWidth'];
    $offset = $config['rowPadding'];

    $showAction = new \View\Product\ShowAction();
    $cartButtonAction = new \View\Cart\ProductButtonAction();
?>

<? foreach ($gridCells as $cell): ?>
<div class="productInner" style="position: absolute;
    <?= 'left: ' . (($cell->getColumn() - 1) *  $step + ($cell->getColumn() - 1) * $offset) . 'px;' ?>
    <?= 'top: ' . (($cell->getRow() - 1) *  $step + ($cell->getRow() - 1) * $offset) . 'px;' ?>
    <?= 'width:' . ($cell->getSizeX() * $step + ($cell->getSizeX() - 1) * $offset) . 'px;' ?>
    <?= 'height:' . ($cell->getSizeY() * $step + ($cell->getSizeY() - 1) * $offset) . 'px;' ?>
">
    <? if (\Model\GridCell\Entity::TYPE_PRODUCT === $cell->getType()): ?>
    <?
        $product = ((isset($productsByUi[$cell->getObjectUi()]) && $productsByUi[$cell->getObjectUi()] instanceof \Model\Product\BasicEntity) ? $productsByUi[$cell->getObjectUi()] : null);
    ?>
        <? if ($product): ?>
            <? if ($product->getLabel()): ?>
                <div class="bProductDescSticker mLeft">
                    <img src="<?= $product->getLabel()->getImageUrl(1) ?>" alt="<?= $helper->escape($product->getLabel()->getName()) ?>" />
                </div>
            <? endif ?>

            <? if ($product->getMainCategory() && 'tchibo' === $product->getMainCategory()->getToken() && !$product->isAvailable() && !$product->hasAvailableModels()): ?>
                <div class="bProductDescSticker">
                    <img src="/images/shild_sold_out.png" alt="Нет в наличии" />
                </div>
            <? endif ?>

            <?= $helper->renderWithMustache('product/show/__grid', $showAction->execute(
                $helper,
                $product,
                [],
                null,
                false,
                $cartButtonAction,
                null,
                3
            )) ?>
        <? endif ?>
    <? elseif (\Model\GridCell\Entity::TYPE_IMAGE === $cell->getType()): ?>
    <?
        $url = $cell->getImageUrl();
        $link = $cell->getUrl();
        $name = 'ok';//$cell->getName();
    ?>
        <? if ($link) { ?> <a href="<?= $link ?>" title="<?= $name ?>"> <? } ?>
            <img src="<?= $url ?>" alt="<?= $name ?>" style="max-width:100%;" />
        <? if ($link) { ?> </a> <? } ?>
    <? endif ?>
</div>
<? endforeach ?>

<?
}; return $f;