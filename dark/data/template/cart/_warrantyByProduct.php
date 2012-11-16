<?php
/**
 * @var $page        \View\Layout
 * @var $cart        \Session\Cart
 * @var $product     \Model\Product\Entity
 * @var $cartProduct \Model\Cart\Product\Entity
 */
?>

<?
$selectedWarranty = false;
foreach ($product->getWarranty() as $warranty) {
    if ($cartProduct->hasWarranty($warranty->getId())) {
        $selectedWarranty = $warranty;
        break;
    }
}
?>

<div class="mBR5 basketServices">
    <div class="service form bBacketServ extWarr mSmall"<? if ($selectedWarranty): ?> style="display:none;"<? endif ?>>
        <table cellspacing="0">
            <tbody>
            <tr>
                <th colspan="3">Для этого товара есть дополнительная гарантия:</th>
            </tr>
                <? foreach ($product->getWarranty() as $warranty) { ?>
            <tr>
                <td><?= $warranty->getName() ?></td>
                <td class="mPrice"></td>
                <td class="mEdit"></td>
            </tr>
                <? } ?>
            <tr>
                <td class="bBlueButton">
                    <a href="" class="link_extWarr">Выбрать гарантию!</a>
                </td>
                <td></td>
                <td></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<? if (!$selectedWarranty): ?>
    <div class="mBR5 basketServices">
        <div class="service form bBacketServ extWarr mBig" style="display:none;">
            <table cellspacing="0">
                <tbody>
                <tr>
                    <th colspan="3">Для этого товара есть дополнительная гарантия:</th>
                </tr>
                <tr>
                    <td class="bBlueButton">
                        <a href="" class="link_extWarr">Выбрать гарантию</a>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
<? endif ?>

<? if ($selectedWarranty) { ?>
    <div class="mBR5 basketServices">
        <div class="service form bBacketServ extWarr mBig" style="display: block;">
            <table cellspacing="0">
                <tbody>
                <tr ref="<?= $selectedWarranty->getId(); ?>">
                    <th colspan="3">Для этого товара выбрана дополнительная гарантия:</th>
                </tr>
                <tr>
                    <td>
                        <span class="ew_title"><?= $selectedWarranty->getName() ?></span>
                        <br>
                        <!--a class="bBacketServ__eMore" href="#">Подробнее об услуге</a-->
                    </td>
                    <td class="mPrice">
                        <span class="price"><?= $selectedWarranty->getPrice() ?></span>&nbsp;<span class="rubl">p</span>
                    </td>
                    <td class="mQuantity" style="font-size: 80%; padding-left: 20px;">
                        <span class="quantity"><?= $cartProduct->getWarrantyById($selectedWarranty->getId())->getQuantity() ?></span>&nbsp;<span>шт</span>
                    </td>
                    <td class="mEdit">
                        <a class="button whitelink ml5 mInlineBlock mVAMiddle" href="<?= $page->url('cart.warranty.delete', array('warrantyId' => $selectedWarranty->getId(), 'productId' => $product->getId())) ?>">Отменить</a>
                    </td>
                </tr>
                <tr>
                    <td class="bBlueButton">
                        <a href="" class="link_extWarr">Выбрать гарантию</a>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
<? } ?>

<?= $page->render('warranty/_selection', array('product' => $product)) ?>
