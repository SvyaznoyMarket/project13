<?php
/**
 * @var $page    \View\Layout
 * @var $user    \Session\User
 * @var $product \Model\Product\Entity
 * @var $cart    \Session\Cart
 */
?>

<?php
    $cart = $user->getCart();
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
                    <?= $warranty->getName() ?><br/>
                    <a href="/warranty_f1#warranty<?= $warranty->getId() ?>">Подробнее об услуге</a>
                </td>
                <td class="bF1Block_eBuy" ref="<?= $warranty->getId() ?>">
                    <span class="bF1Block_ePrice"><?= $page->helper->formatPrice($warranty->getPrice()) ?>&nbsp;	<span class="rubl">p</span></span>
                    <input class="button yellowbutton<?= $cart->hasWarranty($product->getId(), $warranty->getId()) ? ' active' : '' ?>" type="button" value="<?= $cart->hasWarranty($product->getId(), $warranty->getId()) ? 'В корзине' : 'Выбрать' ?>"
                           data-ewid="<?= $warranty->getId() ?>"
                           data-f1title="<?= $warranty->getName() ?>"
                           data-f1price="<?= $warranty->getPrice() ?>"
                           data-url="<?= $page->url('cart.warranty.set', array('productId' => $product->getId(), 'warrantyId' => $warranty->getId(), 'quantity' => 1)) ?>"
                           data-deleteurl="<?= $page->url('cart.warranty.delete', array('productId' => $product->getId(), 'warrantyId' => $warranty->getId())) ?>" />
                </td>
            </tr>
                <?php endforeach ?>

            </tbody>
        </table>
    </div>
</div>
<!-- END всплывающее окно с выбором доп.гарантии-->
<? } ?>