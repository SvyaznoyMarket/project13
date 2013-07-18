<?php
/**
 * @var $page       \View\Layout
 * @var $product    \Model\Product\CompactEntity
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
$inCart = \App::user()->getCart()->hasProduct($product->getId());
$btnText = $inCart ? 'В корзине' : 'Купить';
?>

<div class="goodsbox<? if ($maxHeight): ?> height220<? endif ?>"<? if ($isHidden): ?> style="display:none;"<? endif ?> ref="<?php echo $product->getToken(); ?>" data-quantity="<?php echo empty($totalProducts) ? '' : $totalProducts; ?>" data-category="<?php echo empty($categoryToken) ? '' : $categoryToken; ?>" data-total-pages="<?php echo empty($totalPages) ? '' : $totalPages; ?>" data-category="<?php echo empty($categoryToken) ? '' : $categoryToken; ?>">
    <div class="goodsbox__inner" data-url="<?= $product->getLink() ?>" <?php if (isset($additionalData)) echo 'data-product="' . $page->json($additionalData) . '"' ?>>
    	<h3><a href="<?php echo $product->getLink() ?>"<?php if (!empty($gaEvent)) echo ' data-event="'.$gaEvent.'" data-title="Переход по ссылке" class="gaEvent"'; ?>><?php echo $product->getName() ?></a></h3>
    	 
    	<div class="photo">
			<a href="<?php echo $product->getLink() ?>"<?php if (!empty($gaEvent)) echo ' data-event="'.$gaEvent.'" data-title="Переход по ссылке" class="gaEvent"'; ?>>
			  <img class="mainImg" src="<?php echo $product->getImageUrl() ?>" alt="<?php echo $product->getNameWithCategory() ?>"
				 width="119" height="120"/>
			</a>
		</div>
	    
        <? if ($product->getIsBuyable()): ?>
            <?= $helper->render('cart/__button-product', ['product' => $product, 'class' => 'btnBuy__eLink', 'value' => $btnText]) // Кнопка купить ?>
        <? endif ?>

	    <div class="font18 pb10 mSmallBtns"><span class="price"><?php echo $page->helper->formatPrice($product->getPrice()) ?></span> <span class="rubl">p</span></div>

	    <? if (!$product->getIsBuyable() && $product->getState()->getIsShop()): ?>
        	<div class="notBuying font12">
                <div class="corner"><div></div></div>
                Только в магазинах
            </div>
		<? endif ?>
    </div>
</div>
