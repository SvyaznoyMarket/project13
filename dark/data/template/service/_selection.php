<?php
/**
 * @var $page    \View\Layout
 * @var $product \Model\Product\Entity
 * @var $user    \Session\User
 */

$list = $product->getService();
$listInCart = $user->getCart()->getServicesByProduct($product->getId());
?>
<div class="hideblock bF1Block mGoods" style="display: none;">
    <i class="close" title="Закрыть">Закрыть</i>
    <h2>Добавление услуги F1</h2>
    <?php
    if (count($list) > 3) echo '<div>';
    ?>
    <table>
        <tbody>
        <?php foreach ($list as $service): ?>
        <tr>

            <td class="bF1Block_eInfo"><?php echo $service->getName() ?><br>
                <a href="<?php echo $page->url('service.show', array('serviceToken' => $service->getToken())) ?>">Подробнее об услуге</a>
            </td>
            <td class="bF1Block_eBuy" ref="<?php echo $service->getToken() ?>">
                <?php if ($service->getPrice()) { ?>
                <span class="bF1Block_ePrice">
                <?php echo $page->helper->formatPrice($service->getPrice()) ?>&nbsp;<span class="rubl">p</span>
                </span>
                <?php } ?>
                    <? if (!$service->getIsDelivered() && $service->getIsInShop()) { ?>
                    <span class='bF1Block__eInShop'>доступна в магазине</span>
                    <? } elseif ($user->getRegion()->getHasService() && $service->isInSale() && in_array($service->getId(), $listInCart)) { ?>
                    <input data-f1title="<?= $service->getName() ?>" data-f1price="<?= $service->getPrice() ?>"
                           data-fid="<?= $service->getId() ?>"
                           data-url="<?= $page->url('cart.service.add', array('serviceId' => $service->getId(), 'productId' => $product->getId(), 'quantity' => 1)) ?>"
                           ref="<?= addslashes($service->getToken()) ?>"
                           type="button" class="active button yellowbutton" value="В корзине" />
                    <? } elseif ($user->getRegion()->getHasService() && $service->isInSale()) { ?>
                    <input data-f1title="<?= $service->getName() ?>"
                           data-f1price="<?= $page->helper->formatPrice($service->getPrice()) ?>"
                           data-fid="<?= $service->getId() ?>"
                           data-url="<?= $page->url('cart.service.add', array('serviceId' => $service->getId(), 'productId' => $product->getId(), 'quantity' => 1)) ?>"
                           data-event="BuyF1"
                           data-title="Заказ услуги F1"
                           ref="<?= addslashes($service->getToken()) ?>"
                           type="button" class="button yellowbutton gaEvent" value="Купить услугу" />
                    <? } ?>
                </td>
            </tr>
            <? endforeach ?>
        <tr>
            <th colspan="2"><a href="<?php echo $page->url('service') ?>">Посмотреть все услуги F1</a></th>
        </tr>
        </tbody></table>
    <?php if (count($list) > 3) echo '</div>';
    ?>
</div>