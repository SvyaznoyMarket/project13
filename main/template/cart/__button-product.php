<?php
return function (
    \Helper\TemplateHelper $helper,
    \Model\Product\BasicEntity $product,
    $onClick = null,
    $isRetailRocket = null
) {
    print $helper->renderWithMustache('cart/_button-product', (new \View\Cart\ProductButtonAction())->execute(new \Helper\TemplateHelper(), $product, $onClick, $isRetailRocket));
};