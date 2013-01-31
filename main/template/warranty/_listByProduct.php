<?php
/**
 * @var $page    \View\Layout
 * @var $user    \Session\User
 * @var $product \Model\Product\Entity
 * @var $user    \Session\User
 */
?>

<? if (\App::config()->warranty['enabled']) { ?>
<?
    $warrantiesById = []; foreach ($product->getWarranty() as $warranty) { $warrantiesById[$warranty->getId()] = $warranty; }
    $cartProduct = $user->getCart()->getProductById($product->getId());
    /** @var $cartWarranty \Model\Cart\Warranty\Entity|null */
    $cartWarranty = null;
    if ($cartProduct) {
        $cartWarranties = $cartProduct->getWarranty();
        $cartWarranty = reset($cartWarranties);
    }
    /** @var $warranty \Model\Product\Warranty\Entity|null */
    $warranty = ($cartWarranty && isset($warrantiesById[$cartWarranty->getId()])) ? $warrantiesById[$cartWarranty->getId()] : null;
?>

<?php if ((bool)$warrantiesById): ?>

    <?= $page->render('warranty/_selection', array('product' => $product))?>

    <div class="bBlueButton extWarranty">
        <img alt="Дополнительная гарантия" class="bF1Info_Logo" src="/images/F1_logo_extWarranty.jpg">
        <?php if ($warranty): ?>
            <h3>Вы выбрали гарантию:</h3>
            <div id="ew_look" ref="<?= $warranty->getId() ?>">
                <span class="ew_title"><?= $warranty->getName() ?></span>
                - <span class="ew_price"><?= $page->helper->formatPrice($cartWarranty->getPrice()) ?></span>&nbsp;
                <span class="rubl"> p</span>
                <br>
                <a class="bBacketServ__eMore" href="<?= $page->url('cart.warranty.delete', array('warrantyId' => $warranty->getId(), 'productId' => $product->getId())) ?>">Отказаться</a>
            </div>
        <? else: ?>
            <h3>Дополнительная<br />гарантия</h3>
        <? endif ?>

        <div id="ew_look" style="display:none;">
            <span class="ew_title"></span> - <span class="ew_price"></span>&nbsp;
            <span class="rubl"> p</span>
            <br>
            <a class="bBacketServ__eMore" href="#">Отменить услугу</a>
        </div>
        <a class="link1" href="#">
            Выбрать гарантию
        </a>
    </div>
    <?php endif ?>
<? } ?>