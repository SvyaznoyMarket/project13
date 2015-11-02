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
    $reviewAction = new \View\Product\ReviewCompact2Action();
    ?>

    <? foreach ($gridCells as $cell): ?>
        <?
        /** @var \Model\Product\Entity|null $product */
        $product = ((\Model\GridCell\Entity::TYPE_PRODUCT === $cell->getType()) && (isset($productsByUi[$cell->getObjectUi()]) && $productsByUi[$cell->getObjectUi()] instanceof \Model\Product\Entity) ? $productsByUi[$cell->getObjectUi()] : null);
        $isSoldOut = $product && $product->isSoldOut() && !\App::config()->preview;
        ?>

        <div class="productInner js-gridListing<? if ($isSoldOut): ?> productInner-off<? endif ?>" style="position: absolute;
        <?= 'left: ' . (($cell->getColumn() - 1) *  $step + ($cell->getColumn() - 1) * $offset) . 'px;' ?>
        <?= 'top: ' . (($cell->getRow() - 1) *  $step + ($cell->getRow() - 1) * $offset) . 'px;' ?>
        <?= 'width:' . ($cell->getSizeX() * $step + ($cell->getSizeX() - 1) * $offset) . 'px;' ?>
        <?= 'height:' . ($cell->getSizeY() * $step + ($cell->getSizeY() - 1) * $offset) . 'px;' ?>
            ">
            <? if (\Model\GridCell\Entity::TYPE_PRODUCT === $cell->getType()): ?>
                <? if ($product): ?>
                    <? if ($product->getLabel()): ?>
                        <div class="bProductDescSticker mLeft">
                            <img src="<?= $product->getLabel()->getImageUrl(1) ?>" alt="<?= $helper->escape($product->getLabel()->getName()) ?>" />
                        </div>
                    <? endif ?>

                    <? if ($isSoldOut): ?>
                        <div class="bProductDescSticker stockSticker">
                            <img class="stockSticker_img" src="/images/shild_sold_out.png" alt="Нет в наличии" />
                        </div>
                    <? endif ?>

                    <?= $helper->renderWithMustache('product/show/__grid', $a = $showAction->execute(
                        $helper,
                        $product,
                        null,
                        false,
                        $cartButtonAction,
                        $reviewAction,
                        'product_500'
                    )) ?>
                <? endif ?>
            <? elseif (\Model\GridCell\Entity::TYPE_IMAGE === $cell->getType()): ?>
                <?
                $url = $cell->getImageUrl();
                $link = $cell->getUrl();
                $name = $cell->getName();
                ?>
                <? if ($link) { ?> <a href="<?= $link ?>" title="<?= $name ?>"> <? } ?>
                <img src="<?= $url ?>" alt="<?= $name ?>" style="max-width:100%;" />
                <? if ($link) { ?> </a> <? } ?>
            <? endif ?>
        </div>
    <? endforeach ?>

<?
}; return $f;