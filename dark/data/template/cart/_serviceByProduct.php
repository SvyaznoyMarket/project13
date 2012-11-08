<?
/**
 * @var $page        \View\Layout
 * @var $cart        \Session\Cart
 * @var $product     \Model\Product\Entity
 * @var $cartProduct \Model\Cart\Product\Entity
 */
?>

<?
$hasSelected = false;
foreach ($product->getService() as $service) {
    if ($cartProduct->hasService($service->getId())) {
        $hasSelected = true;
        break;
    }
}
?>

<div class="mBR5 basketServices">

    <div class="service form bBacketServ F1 mSmall"<? if ($hasSelected): ?> style="display:none;"<? endif ?>>
        <table cellspacing="0">
            <tbody>
                <tr>
                    <th colspan="3">Для этого товара есть услуги:</th>
                </tr>

                <? $i = 0; foreach ($product->getService() as $service): ?>
                    <? if ($i == 2) break; ?>
                    <tr>
                        <td><?= $service->getName() ?></td>
                        <td class="mPrice"></td>
                        <td class="mEdit"></td>
                    </tr>
                <? $i++; endforeach ?>

                <tr>
                    <td class="bBlueButton"><a class="link1" href="">Выбрать услуги</a></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<? if (!$hasSelected): ?>
    <div class="mBR5 basketServices">
        <div class="service form bBacketServ F1 mBig" style="display:none;">
            <table cellspacing="0">
                <tbody>
                <tr>
                    <th colspan="3">Для этого товара есть услуги:</th>
                </tr>
                <tr>
                    <td class="bBlueButton"><a class="link1" href="">Выбрать услуги</a></td>
                    <td></td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
<? endif ?>


<? if ($hasSelected): ?>
    <div class="mBR5 basketServices">
        <div class="service form bBacketServ F1 mBig">
            <table cellspacing="0">
                <tbody>
                <tr>
                    <th colspan="3">Для этого товара есть услуги:</th>
                </tr>
                <? foreach ($product->getService() as $service): ?>
                    <?
                    if (!$cartProduct->hasService($service->getId())) continue;
                    $cartService = $cartProduct->getServiceById($service->getId());
                    ?>

                    <tr ref="<?= $service->getToken() ?>">
                        <td>
                            <?= $service->getName() ?><br>
                            <a class="bBacketServ__eMore" href="<?= $page->url('service.show', array('serviceToken' => $service->getToken())) ?>">Подробнее об услуге</a>
                        </td>
                        <td class="mPrice"><span class="price"><?= $page->helper->formatPrice($cartService->getTotalPrice()) ?></span>&nbsp;<span class="rubl">p</span></td>
                        <td class="mEdit">
                            <div class="numerbox mInlineBlock mVAMiddle">
                                <? if ($cartService->getQuantity() > 1): ?>
                                <a href="<?= $page->url('cart.service.add', array('serviceId' => $service->getId(), 'quantity' => -1, 'productId' => $product->getId())) ?>">
                                    <b title="Уменьшить" class="ajaless"></b>
                                </a>
                                <? else: ?>
                                <a href="#" ref="<?= $page->url('cart.service.add', array('serviceId' => $service->getId(), 'quantity' => -1, 'productId' => $product->getId())) ?>">
                                    <b title="Уменьшить" class="ajaless"></b>
                                </a>
                                <? endif ?>
                                <span class="ajaquant"><?= $cartService->getQuantity() ?> шт.</span>
                                <a href="<?= $page->url('cart.service.add', array('serviceId' => $service->getId(), 'productId' => $product->getId(), 'quantity' => 1)) ?>">
                                    <b title="Увеличить" class="ajamore"></b>
                                </a>
                            </div>
                            <a class="button whitelink ml5 mInlineBlock mVAMiddle" href="<?= $page->url('cart.service.delete', array('serviceId' => $service->getId(), 'productId' => $product->getId())) ?>">Отменить</a>
                        </td>
                    </tr>
                <? endforeach ?>
                <tr>
                    <td class="bBlueButton"><a class="link1" href="">Выбрать услуги</a></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
<? endif ?>

<?= $page->render('service/_selection', array('product' => $product)) ?>
