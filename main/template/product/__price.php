<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product
) {

    if (0 === $product->getPrice()) {
        return;
    }
?>

<? if ($product->getPriceOld()): ?>
    <div class="priceOld"><span><?= $helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span></div>
<? elseif (\App::config()->product['showAveragePrice'] && !$product->getPriceOld() && $product->getPriceAverage()): ?>

<? endif ?>
<span>
    <div class="bPrice5321a13ebb1e5 bInputList">
    	<input class="jsCustomRadio bCustomInput mCustomRadioBig jsProductCreditRadio" type="radio" id="price" name="product_credit_payment" value="off" checked />

    	<label for="price" class="bCustomLabel mCustomLabelRadioBig mChecked">
	    	<strong itemprop="price" class="jsPrice"><?= $helper->formatPrice($product->getPrice()) ?></strong>
            <meta itemprop="priceCurrency" content="RUB" />
	    	<span class="rubl">p</span>
	    </label>
    </div>
</span>
<? };