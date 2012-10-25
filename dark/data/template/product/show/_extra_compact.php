<?php
/**
 * @var $page      \View\Layout
 * @var $product   \Model\Product\CompactEntity
 * @var $isHidden  bool
 * @var $maxHeight bool
 * */
?>

<?php
$isHidden = isset($isHidden) && $isHidden;
$maxHeight = isset($maxHeight) && $maxHeight;
?>

<div class="goodsbox<? if ($maxHeight): ?> height220<? endif ?>"<? if ($isHidden): ?> style="display:none;"<? endif ?>>
    <div class="goodsbox__inner" ref="<?php echo $product->getToken(); ?>">
    	<div class="photo">
			<a href="<?php echo $product->getLink() ?>">
			  <img class="mainImg" src="<?php echo $product->getImageUrl() ?>" alt="<?php echo $product->getNameWithCategory() ?>"
				title="<?php echo $product->getNameWithCategory() ?>" width="119" height="120"/>
			</a>
		</div>
	    <div class="goodsbox__rating rate<?= round($product->getRating())?>">
	    	<div class="fill"></div>
	    </div>
	    <h3><a href="<?php echo $product->getLink() ?>"><?php echo $product->getName() ?></a></h3>
		<div class="goodsbar mSmallBtns mR">
          <?php echo $page->render('cart/_button', array('product' => $product)) ?>
        </div>
	    <div class="font18 pb10 mSmallBtns"><span class="price"><?php echo $page->helper->formatPrice($product->getPrice()) ?></span> <span class="rubl">p</span></div>
    </div>
</div>
