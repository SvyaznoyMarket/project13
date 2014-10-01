<?php
/**
 * @var $kits       \Model\Product\Entity[]
 * @var $product    \Model\Product\Entity
 */
return function (
    \Helper\TemplateHelper $helper,
    array $kits,
    \Model\Product\Entity $product
) {

    $products = [];
    $showAction = new \View\Product\ShowAction();
    foreach ($kits as $kit) {
        $products[] = $showAction->execute($helper, $kit, []);
    }

    ?>

    <div>

        <h3 class="bHeadSection"><?= $product->getName() ?> входит в наборы</h3>

        <ul class="bListing clearfix js-listing">
            <?= $helper->renderWithMustache('product/list/_compact.mustache', ['products' => $products]) ?>
        </ul>

    </div>

<? } ?>