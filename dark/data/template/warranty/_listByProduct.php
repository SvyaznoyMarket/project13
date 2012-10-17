<?php
/**
 * @var $page    \View\Layout
 * @var $product \Model\Product\Entity
 * @var $user    \Session\User
 */

$user = \App::user();
?>

<?php if (\App::config()->warranty['enabled']) { ?>
<?php $warrantiesById = array(); foreach ($product->getWarranty() as $warranty) { $warrantiesById[$warranty->getId()] = $warranty; } ?>

<?php if ((bool)$warrantiesById): ?>

    <?php echo $page->render('warranty/_selection', array('product' => $product))?>

    <div class="bBlueButton extWarranty">
        <img alt="Дополнительная гарантия" class="bF1Info_Logo" src="/images/F1_logo_extWarranty.jpg">
        <?php if (in_array($warranty->getId(), $user->getCart()->getWarrantyByProduct($product->getId()))) { ?>
        <h3>Вы выбрали гарантию:</h3>
        <div id="ew_look" ref="<?php echo $warranty->getId() ?>">
            <span class="ew_title"><?php echo $warrantiesById[$warranty->getId()]->getName() ?></span>
            - <span class="ew_price"><?php echo $page->helper->formatPrice($warranty->getPrice()) ?></span>&nbsp;
            <span class="rubl"> p</span>
            <br>
            <a class="bBacketServ__eMore" href="<?php echo $page->url('cart.warranty.delete', array('warrantyId' => $warranty->getId(), 'productId' => $product->getId())) ?>">Отказаться</a>
        </div>
        <?php } else { ?>
        <h3>Дополнительная<br />гарантия</h3>
        <?php } ?>
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