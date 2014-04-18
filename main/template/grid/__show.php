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
    $config = \App::config()->tchibo;

    $step = $config['rowWidth'];
    $offset = $config['rowPadding'];

    $showAction = new \View\Product\ShowAction();
    $cartButtonAction = new \View\Cart\ProductButtonAction();
?>

<? foreach ($gridCells as $cell): ?>
<div class="productInner" style="position: absolute; overflow: hidden;
    <?= 'left: ' . (($cell->getColumn() - 1) *  $step + ($cell->getColumn() - 1) * $offset) . 'px;' ?>
    <?= 'top: ' . (($cell->getRow() - 1) *  $step + ($cell->getRow() - 1) * $offset) . 'px;' ?>
    <?= 'width:' . ($cell->getSizeX() * $step + ($cell->getSizeX() - 1) * $offset) . 'px;' ?>
    <?= 'height:' . ($cell->getSizeY() * $step + ($cell->getSizeY() - 1) * $offset) . 'px;' ?>
">
    <? if (\Model\GridCell\Entity::TYPE_PRODUCT === $cell->getType()): ?>
    <?
        $product = ((isset($productsById[$cell->getId()]) && $productsById[$cell->getId()] instanceof \Model\Product\BasicEntity) ? $productsById[$cell->getId()] : null);
    ?>
        <? if ($product): ?>
            <? if ($product->getMainCategory() && 'tchibo' === $product->getMainCategory()->getToken() && !$product->getIsBuyable()): ?>
                <div class="bProductDescSticker">
                    <img src="/images/shild sold out.png" alt="Нет в наличии" />
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
        $content = $cell->getContent();
        $url = !empty($content['url']) ? $content['url'] : null;
        $link = !empty($content['link']) ? $content['link'] : null;
        $name = !empty($content['name']) ? $content['name'] : '';
    ?>
        <? if ($link) { ?> <a href="<?= $link ?>" title="<?= $name ?>"> <? } ?>
            <img src="<?= $url ?>" alt="<?= $name ?>" style="max-width:100%;" />
        <? if ($link) { ?> </a> <? } ?>
    <? endif ?>
</div>
<? endforeach ?>

<?
}; return $f;