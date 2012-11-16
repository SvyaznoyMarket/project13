<?php
/**
 * @var $page     \View\Layout
 * @var $product  \Model\Product\CompactEntity
 * @var $isHidden bool
 * @var $kit      \Model\Product\Kit\Entity
 * */
?>

<?php
$isHidden = isset($isHidden) && $isHidden;
$hasModel = (isset($hasModel) ? $hasModel : true) && $product->getModel() && (bool)$product->getModel()->getProperty();
?>

<div class="goodsbox"<? if ($isHidden): ?> style="display:none;"<? endif ?>>
    <div class="goodsbox__inner" ref="<?= $product->getToken(); ?>" data-url="<?= $product->getLink() ?>">
    	<div class="photo">
	        <a href="<?= $product->getLink() ?>">
	            <? if (!empty($kit) && $kit->getCount()): ?>
	                <div class="bLabelsQuantity" src="/images/quantity_shild.png"><?= $kit->getCount() ?> шт.</div>
	            <? endif ?>
	
	            <? if ($label = $product->getLabel()): ?>
	                <img class="bLabels" src="<?= $label->getImageUrl() ?>" alt="<?= $label->getName() ?>"/>
	            <? endif ?>
	
	            <img class="mainImg" src="<?= $product->getImageUrl(2) ?>" alt="<?= $product->getNameWithCategory() ?>" title="<?= $product->getNameWithCategory() ?>" width="160" height="160"/>
	        </a>
	    </div>
	    <div class="goodsbox__rating rate<?= round($product->getRating())?>">
	    	<div class="fill"></div>
	    </div>
	    <h3><a href="<?= $product->getLink() ?>"><?= $product->getName() ?></a></h3>
		<? if ($product->getIsBuyable()): ?>
        <div class="goodsbar mSmallBtns mR">
            <?= $page->render('cart/_button', array('product' => $product)) ?>
        </div>
        <? endif ?>
	    <div class="font18 pb10 mSmallBtns">
            <span class="price"><?= $page->helper->formatPrice($product->getPrice()) ?></span> <span class="rubl">p</span>
        </div>
	    <? if ($hasModel): ?>
        <a href="<?= $product->getLink() ?>">
            <div class="bListVariants">
                Доступно в разных вариантах<br>
                (<?= $product->getModel()->getVariations() ?>)
            </div>
        </a>
        <? endif ?>
    </div>
</div>