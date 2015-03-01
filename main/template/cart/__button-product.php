<?php

return function (
    \Helper\TemplateHelper $helper,
    \Model\Product\BasicEntity $product,
    $onClick = null,
    $isRetailRocket = null,
    $sender = [],
    $noUpdate = false,
    $location = null,
    $reserveAsBuy = false,
    $sender2 = ''
) {
?>
    <?= $helper->renderWithMustache('cart/_button-product', (
        new \View\Cart\ProductButtonAction())->execute(
            new \Helper\TemplateHelper(),
            $product,
            $onClick,
            $isRetailRocket,
            $sender,
            $noUpdate,
            $location,
            $reserveAsBuy,
            $sender2
        )
    ) ?>
<? };