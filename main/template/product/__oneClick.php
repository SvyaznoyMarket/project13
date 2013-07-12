<?php

return function(
    \Model\Product\BasicEntity $product,
    \Helper\TemplateHelper $helper
) {
    if (!$product->getIsBuyable()) {
        return '';
    }

    $user = \App::user();
?>

    <div class="bWidgetBuy__eClick">
        <a
            href="#"
            class="jsOrder1click"
            data-model="<?= $helper->json([
                'jsref'        => $product->getToken(),
                'jstitle'      => $product->getName(),
                'jsprice'      => $product->getPrice(),
                'jsimg'        => $product->getImageUrl(3),
                'jsbimg'       => $product->getImageUrl(2),
                'jsshortcut'   => $product->getArticle(),
                'jsitemid'     => $product->getId(),
                'jsregionid'   => $user->getRegion()->getId(),
                'jsregionName' => $user->getRegion()->getName(),
                'jsstock'      => 10,
            ]) ?>"
            link-output="<?= $helper->url('order.1click', ['product' => $product->getToken()]) ?>"
            link-input="<?= $helper->url('product.delivery_1click') ?>"
            >Купить быстро в 1 клик</a>
    </div>
    <form id="order1click-form" action="<?= $helper->url('order.1click', ['product' => $product->getBarcode()]) ?>" method="post"></form>


<? };