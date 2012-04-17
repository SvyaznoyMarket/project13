<div class="fr ar pb15">
    <div class="goodsbarbig mSmallBtns" ref="<?php echo $product->token ?>" data-value='<?php echo $json ?>'>

        <div class='bCountSet'>
            <?php if (!$product->cart_quantity): ?>
            <a class='bCountSet__eP' href>+</a><a class='bCountSet__eM' href>-</a>
            <?php else: ?>
            <a class='bCountSet__eP disabled' href>&nbsp;</a><a class='bCountSet__eM disabled' href>&nbsp;</a>
            <?php endif ?>
            <span><?php echo $product->cart_quantity ? $product->cart_quantity : 1 ?> шт.</span>
        </div>

        <?php echo include_component('cart', 'buy_button', array('product' => $product, 'quantity' => 1, 'soa' => 1)) ?>
    </div>
    <?php if (false && $product->is_insale && $sf_user->getRegion('region')->is_default): ?>
    <div class="pb5"><strong><a  onClick="_gaq.push(['_trackEvent', 'QuickOrder', 'Open']);" href="<?php echo url_for('order_1click', array('product' => $product->barcode));  ?>" class="red underline order1click-link">Купить быстро в 1 клик</a></strong></div>
    <?php endif ?>
</div>
<div class="fr mBuyButtonBottom">
    <div class="pb10"><?php include_partial('productSoa/price', array('price' => $product->getFormattedPrice())) ?></div>
</div>
<div class="fl mBuyButtonBottom ">
    <h2 class="bold"><?php echo $product->name ?></h2>
</div>
<div class="clear"></div>
<div class="mb15"></div>