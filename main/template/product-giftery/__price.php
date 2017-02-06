<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product
) {

    if (0 === $product->getPrice()) {
        return;
    }
?>

        <div class="product-card-price">
            <span class="product-card-price__inner">от <strong><?= \App::config()->partners['Giftery']['lowestPrice'] ?></strong>
                <span class="rubl">p</span>
            </span>
        </div>
<? };