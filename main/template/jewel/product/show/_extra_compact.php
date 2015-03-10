<?php
/**
 * @var $page       \View\Layout
 * @var $product    \Model\Product\Entity
 * @var $isHidden   bool
 * @var $maxHeight  bool
 * @var $gaEvent    string
 * @var $totalPages int
 * */
?>

<?php
$helper = new \Helper\TemplateHelper();
$isHidden = isset($isHidden) && $isHidden;
$maxHeight = isset($maxHeight) && $maxHeight;
$gaEvent = isset($gaEvent) ? $gaEvent : null;
$additionalData = isset($additionalData) ? $additionalData : null;
?>

<div class="goodsbox<? if ($maxHeight): ?> height220<? endif ?> js-goodsbox"<? if ($isHidden): ?> style="display:none;"<? endif ?> data-quantity="<?php echo empty($totalProducts) ? '' : $totalProducts; ?>" data-category="<?php echo empty($categoryToken) ? '' : $categoryToken; ?>" data-total-pages="<?php echo empty($totalPages) ? '' : $totalPages; ?>" data-category="<?php echo empty($categoryToken) ? '' : $categoryToken; ?>">
    <div class="goodsbox__inner js-goodsboxContainer" data-url="<?= $product->getLink() ?>" <?php if (isset($additionalData)) echo 'data-product="' . $page->json($additionalData) . '"' ?>>
    	<h3><a href="<?php echo $product->getLink() ?>"<?php if (!empty($gaEvent)) echo ' data-event="'.$gaEvent.'" data-title="Переход по ссылке" class="gaEvent"'; ?>><?php echo $product->getName() ?></a></h3>
    	 
    	<div class="photo">
			<a href="<?php echo $product->getLink() ?>"<?php if (!empty($gaEvent)) echo ' data-event="'.$gaEvent.'" data-title="Переход по ссылке" class="gaEvent"'; ?>>
			  <img class="mainImg" src="<?php echo $product->getImageUrl() ?>" alt="<?php echo $product->getNameWithCategory() ?>"
				 width="119" height="120"/>
			</a>
		</div>
	    
        <? if ($product->getIsBuyable()): ?>
            <?= $helper->render('cart/__button-product', ['product' => $product]) // Кнопка купить ?>
        <? endif ?>

	    <div class="font18 pb10 mSmallBtns"><span class="price"><?php echo $page->helper->formatPrice($product->getPrice()) ?></span> <span class="rubl">p</span></div>

        <?= $page->render('product/show/__corner_features', ['product' => $product]) ?>
    </div>
</div>
