<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product
) {

    if (0 === $product->getPrice()) {
        return;
    }

    $user = \App::user();
?>

<? if ($product->getPriceOld() && !$user->getRegion()->getHasTransportCompany()): ?>
    <div class="priceOld"><span><?= $helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span></div>
<? elseif (\App::config()->product['showAveragePrice'] && !$product->getPriceOld() && $product->getPriceAverage()): ?>

<? endif ?>
<span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
    <div itemprop="price" class="bPrice5321a13ebb1e5 bInputList">
    	<input class="jsCustomRadio bCustomInput mCustomRadioBig" type="radio" id="price" name="price_or_credit" />

    	<label for="price" class="bCustomLabel mCustomLabelRadioBig mChecked">
	    	<strong class="jsPrice"><?= $helper->formatPrice($product->getPrice()) ?></strong> 
	    	<span class="rubl">p</span>
	    </label>
    </div>
</span>
<? };