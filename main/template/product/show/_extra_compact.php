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
$isHidden = isset($isHidden) && $isHidden;
$maxHeight = isset($maxHeight) && $maxHeight;
$gaEvent = isset($gaEvent) ? $gaEvent : null;
$additionalData = isset($additionalData) ? $additionalData : null;
$productLink = $product->getLink().($page->hasGlobalParam('sender')?(false === strpos($product->getLink(), '?') ? '?' : '&') . 'sender='.$page->getGlobalParam('sender').'|'.$product->getId():'');
?>

<div class="goodsbox<? if ($maxHeight): ?> height220<? endif ?> js-goodsbox"<? if ($isHidden): ?> style="display:none;"<? endif ?> data-quantity="<?php echo empty($totalProducts) ? '' : $totalProducts; ?>" data-category="<?php echo empty($categoryToken) ? '' : $categoryToken; ?>" data-total-pages="<?php echo empty($totalPages) ? '' : $totalPages; ?>" data-category="<?php echo empty($categoryToken) ? '' : $categoryToken; ?>">
    <div class="goodsbox__inner js-goodsboxContainer" data-url="<?= $productLink ?>" <?php if (isset($additionalData)) echo 'data-product="' . $page->json($additionalData) . '"' ?>>
    	<div class="photo">
			<a href="<?= $productLink ?>"<?php if (!empty($gaEvent)) echo ' data-event="'.$gaEvent.'" data-title="Переход по ссылке" class="gaEvent"'; ?>>
			  <img class="mainImg" src="<?php echo $product->getImageUrl() ?>" alt="<?php echo $product->getNameWithCategory() ?>"
				title="<?php echo $product->getNameWithCategory() ?>" width="119" height="120"/>
			</a>
		</div>

    <? if (\App::config()->product['reviewEnabled']): ?>
        <?= $page->render('product/_reviewsStarsCompact', ['product' => $product, 'twoLines' => true]) ?>
    <? endif ?>

    <h3><a href="<?= $productLink ?>"<?php if (!empty($gaEvent)) echo ' data-event="'.$gaEvent.'" data-title="Переход по ссылке" class="gaEvent"'; ?>><?php echo $product->getName() ?></a></h3>
        <?= \App::closureTemplating()->render('cart/__button-product', ['product' => $product]) ?>
	    <div class="font18 pb10 mSmallBtns"><span class="price"><?php echo $page->helper->formatPrice($product->getPrice()) ?></span> <span class="rubl">p</span></div>
        <?= $page->render('product/show/__corner_features', ['product' => $product]) ?>
    </div>
</div>
