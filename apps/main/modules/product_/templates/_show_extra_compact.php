<?php
/**
 * @var $item ProductEntity
 * @var $ii
 * @var $maxPerPage
 * @var $marker string
 */

$title = $item->getName();
if($main = $item->getMainCategory())
  $title .= ' - '.$main->getName();
$marker = isset($marker) ? $marker : null;
?>

<div class="goodsbox"<?php echo (isset($ii) && $ii > $maxPerPage) ? ' style="display:none;"' : '' ?>>
    <div class="goodsbox__inner">
    	<div class="photo">
	        <a href="<?php echo $item->getLink().(!empty($marker) ? $marker : '') ?>">
      			<img src="<?php echo $item->getMediaImageUrl() ?>" alt="<?php echo $title ?>" title="<?php echo $title ?>" width="119" height="120"/>
			</a>
	    </div>
		<div class="goodsbox__rating rate<?= round($item->getRating())?>">
	    	<div class="fill"></div>
	    </div>
	    <h3><a href="<?php echo $item->getLink().(!empty($marker) ? $marker : '') ?>"><?php echo $item->getName() ?></a></h3>
		<div class="goodsbar mSmallBtns mR">
			<?php render_partial('cart_/templates/_buy_button.php', array('item' => $item)) ?>
		</div>
	    <div class="font18 pb10 mSmallBtns">
	        <span class="price"><?php echo formatPrice($item->getPrice()) ?></span> <span class="rubl">p</span>
	    </div>
    </div>
</div>
