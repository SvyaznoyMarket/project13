<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product
) {
    $data = [
        'id'      => $product->getId(),
        'ui'      => $product->getUi(),
        'token'   => $product->getToken(),
        'article' => $product->getArticle(),
        'barcode' => $product->getBarcode(),
        'name'    => $product->getName(),
        'price'   => $product->getPrice(),
        'image'   => [
            'default' => $product->getMainImageUrl('product_500'),
            'big'     => $product->getMainImageUrl('product_160'),
        ],
        'category'  => $product->getCategory(),
        'stockState'  =>
            $product->getIsBuyable()
            ? 'in stock'
            : (
                ($product->getState() && $product->getState()->getIsShop())
                ? 'at shop'
                : 'out of stock'
            )
        ,
    ];
?>
    <div id="jsProductCard" data-value="<?= $helper->json($data) ?>" data-ecommerce='<?= $product->ecommerceData() ?>'></div>
<? };