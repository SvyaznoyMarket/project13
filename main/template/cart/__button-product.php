<?php

return function (
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    $onClick = null,
    $sender = [],
    $noUpdate = false,
    $location = null,
    $sender2 = ''
) {
?>
    <?= $helper->renderWithMustache('cart/_button-product', (new \View\Cart\ProductButtonAction())->execute(
            new \Helper\TemplateHelper(),
            $product,
            $onClick,
            $sender,
            $noUpdate,
            $location,
            $sender2
        )) ?>
<? };