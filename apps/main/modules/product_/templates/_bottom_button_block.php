<?php
/**
 * @var $product ProductEntity
 */
$json = array (
    'jsref' => $product->getToken(),
    'jstitle' => $product->getName(),
    'jsprice' => $product->getPrice(),
    'jsimg' => $product->getMediaImageUrl(3),
);
?>
<div class="line"></div>
<div class="fr ar">
  <?php if ( $product->getState()->getIsBuyable()): ?>
    <div class="goodsbarbig mSmallBtns" ref="<?php echo $product->getToken() ?>" data-value='<?php echo json_encode( $json ) ?>'>

        <div class='bCountSet'>
            <?php if (!$product->getCartQuantity()): ?>
            <a class='bCountSet__eP' href>+</a><a class='bCountSet__eM' href>-</a>
            <?php else: ?>
            <a class='bCountSet__eP disabled' href>&nbsp;</a><a class='bCountSet__eM disabled' href>&nbsp;</a>
            <?php endif ?>
            <span><?php echo $product->getCartQuantity() ? $product->getCartQuantity() : 1 ?> шт.</span>
        </div>

        <?php render_partial('cart_/templates/_buy_button.php', array('item' => $product)) ?>
    </div>
  <?php else: ?>
    <p class="font16 orange">Для покупки товара<br />обратитесь в Контакт-сENTER</p>
  <?php endif ?>
</div>
<div class="fr mBuyButtonBottom">
    <div class="pb10"><?php render_partial('product_/templates/_price.php', array('price' => formatPrice($product->getPrice()))) ?></div>
</div>
<div class="fl mBuyButtonBottom onleft" >
    <h2 class="bold"><?php echo $product->getName() ?></h2>
</div>