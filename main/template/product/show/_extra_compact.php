<?php
/**
 * @var $page      \View\Layout
 * @var $product   \Model\Product\CompactEntity
 * @var $isHidden  bool
 * @var $maxHeight bool
 * @var $gaEvent   string
 * */
?>

<?php
$isHidden = isset($isHidden) && $isHidden;
$maxHeight = isset($maxHeight) && $maxHeight;
$gaEvent = isset($gaEvent) ? $gaEvent : null;
$additionalData = isset($additionalData) ? $additionalData : null;
?>

<div class="goodsbox<? if ($maxHeight): ?> height220<? endif ?>"<? if ($isHidden): ?> style="display:none;"<? endif ?> ref="<?php echo $product->getToken(); ?>">
    <div class="goodsbox__inner" data-url="<?= $product->getLink() ?>" <?php if (isset($additionalData)) echo 'data-product="'.$page->json($additionalData).'"'; ?>>
    	<div class="photo">
			<a href="<?php echo $product->getLink() ?>"<?php if (!empty($gaEvent)) echo ' data-event="'.$gaEvent.'" data-title="Переход по ссылке" class="gaEvent"'; ?>>
			  <img class="mainImg" src="<?php echo $product->getImageUrl() ?>" alt="<?php echo $product->getNameWithCategory() ?>"
				title="<?php echo $product->getNameWithCategory() ?>" width="119" height="120"/>
			</a>
		</div>
	    <h3><a href="<?php echo $product->getLink() ?>"<?php if (!empty($gaEvent)) echo ' data-event="'.$gaEvent.'" data-title="Переход по ссылке" class="gaEvent"'; ?>><?php echo $product->getName() ?></a></h3>
		<div class="goodsbar mSmallBtns mR">
          <?= $page->render('cart/_button', array('product' => $product, 'disabled' => !$product->getIsBuyable())) ?>
        </div>
	    <div class="font18 pb10 mSmallBtns"><span class="price"><?php echo $page->helper->formatPrice($product->getPrice()) ?></span> <span class="rubl">p</span></div>
	    <? if (!$product->getIsBuyable() && $product->getState()->getIsShop()): ?>
        	<div class="notBuying font12">
                <div class="corner"><div></div></div>
                Только в магазинах
            </div>
		<? endif ?>
    </div>
</div>
