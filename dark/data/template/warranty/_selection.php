<?php
/**
 * @var $page    \View\Layout
 * @var $product \Model\Product\Entity
 * @var $cart    \Session\Cart
 */
?>

<?php
    $cart = \App::user()->getCart();
?>

<?php if (\App::config()->warranty['enabled']) { ?>
<!-- всплывающее окно с выбором доп.гарантии-->
<div class="hideblock mGoods extWarranty">
    <i class="close" title="Закрыть">Закрыть</i>
    <h2>Выбор дополнительной гарантии</h2>
    <div>
        <table>
            <tbody>

                <?php foreach($product->getWarranty() as $warranty): ?>
            <tr>
                <td class="bF1Block_eInfo">
                    <?php echo $warranty->getName() ?><br>
                    <a href="#">Подробнее об услуге</a>
                </td>
                <td class="bF1Block_eBuy">
                    <span class="bF1Block_ePrice"><?php echo $warranty->getPrice() ?>&nbsp;	<span class="rubl">p</span></span>
                    <input class="button yellowbutton<?php echo $cart->hasWarranty($product->getId(), $warranty->getId()) ? ' active' : '' ?>" type="button" value="<?php echo $cart->hasWarranty($product->getId(), $warranty->getId()) ? 'Выбрана' : 'Выбрать' ?>"
                           data-ewid="<?php echo $warranty->getId() ?>"
                           data-f1title="<?php echo $warranty->getName() ?>"
                           data-f1price="<?php echo $warranty->getPrice() ?>"
                           data-url="<?php echo $page->url('cart.warranty.set', array('productId' => $product->getId(), 'warrantyId' => $warranty->getId())) ?>"
                           data-deleteurl="<?php echo $page->url('cart.warranty.delete', array('productId' => $product->getId(), 'warrantyId' => $warranty->getId())) ?>" />
                </td>
            </tr>
                <?php endforeach ?>

            </tbody>
        </table>
    </div>
</div>
<!-- END всплывающее окно с выбором доп.гарантии-->
<? } ?>