<?php
/**
 * @var $page    \View\Layout
 * @var $product \Model\Product\Entity
 * @var $service \Model\Product\Service\Entity
 * @var $user    \Session\User
 */
?>

<?
$services = $product->getService();
$cartProduct = $user->getCart()->getProductById($product->getId());
$cartServicesById = $cartProduct ? $cartProduct->getService() : [];
?>

<div class="hideblock bF1Block mGoods" style="display: none;">
    <i class="close" title="Закрыть">Закрыть</i>

    <h2>Добавление услуги F1</h2>
    <? if (count($services) > 3): ?><div><? endif ?>
    <table>
        <tbody>
            <? foreach ($services as $service): ?>
            <tr>

                <td class="bF1Block_eInfo"><?= $page->escape($service->getName()) ?><br/>
                    <a href="<?= $page->url('service.show', array('serviceToken' => $service->getToken())) ?>">Подробнее об услуге</a>
                </td>
                <td class="bF1Block_eBuy" ref="<?= $service->getToken() ?>">
                    <? if ($service->getPrice()): ?>
                    <span class="bF1Block_ePrice">
                        <?= $page->helper->formatPrice($service->getPrice()) ?>&nbsp;<span class="rubl">p</span>
                    </span>
                    <? endif ?>

                    <? if (!$service->getIsDelivered() && $service->getIsInShop()) { ?>
                    <span class='bF1Block__eInShop'>доступна в магазине</span>
                    <? } elseif ($user->getRegion()->getHasService() && $service->isInSale() && $cartProduct && $cartProduct->hasService($service->getId())) { ?>
                    <input data-f1title="<?= $page->escape($service->getName()) ?>" data-f1price="<?= $service->getPrice() ?>"
                           data-fid="<?= $service->getId() ?>"
                           data-url="<?= $page->url('cart.service.add', array('serviceId' => $service->getId(), 'productId' => $product->getId(), 'quantity' => 1)) ?>"
                           data-f1token="<?= $service->getToken() ?>"
                           ref="<?= addslashes($service->getToken()) ?>"
                           type="button" class="active button yellowbutton" value="В корзине" />
                    <? } elseif ($user->getRegion()->getHasService() && $service->isInSale()) { ?>
                    <input data-f1title="<?= $page->escape($service->getName()) ?>"
                           data-f1price="<?= $page->helper->formatPrice($service->getPrice()) ?>"
                           data-fid="<?= $service->getId() ?>"
                           data-url="<?= $page->url('cart.service.add', array('serviceId' => $service->getId(), 'productId' => $product->getId(), 'quantity' => 1)) ?>"
                           data-f1token="<?= $service->getToken() ?>"
                           data-event="BuyF1"
                           data-title="Заказ услуги F1"
                           ref="<?= addslashes($service->getToken()) ?>"
                           type="button" class="button yellowbutton gaEvent" value="Купить услугу" />
                    <? } ?>
                </td>
            </tr>
            <? endforeach ?>

            <tr>
                <th colspan="2"><a href="<?= $page->url('service') ?>">Посмотреть все услуги F1</a></th>
            </tr>
        </tbody>
    </table>
    <? if (count($services) > 3): ?></div><? endif ?>
</div>