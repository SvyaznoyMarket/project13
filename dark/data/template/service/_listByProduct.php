<?php
/**
 * @var $page    \View\Layout
 * @var $user    \Session\User
 * @var $product \Model\Product\Entity
 */

$services = $product->getService();
$cartProduct = $user->getCart()->getProductById($product->getId());
$cartServicesById = $cartProduct ? $cartProduct->getService() : array();
?>
<? if ((bool)$services): ?>

    <?= $page->render('service/_selection', array('product' => $product)) ?>

    <div class="bF1Info bBlueButton">
        <img class="bF1Info_Logo" src="/images/f1info.png" alt="Улуги F1"/>
        <script type="text/html" id="f1look">
            <div ref="<%=fid%>">
                <%=f1title%> - <%=f1price%>&nbsp;
                <span class="rubl"> p</span>
                <br>
                <a class="bBacketServ__eMore"
                   href="<?= $page->url('cart.service.delete', array('serviceId' => 'F1ID', 'productId' => $product->getId()));?>">Отменить
                    услугу</a>
            </div>
        </script>
        <? if (count($cartServicesById)) { ?>
        <h3>Вы добавили услуги:</h3>
        <? foreach ($services as $service): if ($cartProduct && $cartProduct->hasService($service->getId())): ?>
            <div ref="<?= $service->getToken();?>">
                <?= $service->getName() ?> - <?=  $page->helper->formatPrice($service->getPrice()) ?>
                &nbsp;<span class="rubl">p</span><br>
                <a class="bBacketServ__eMore"
                   href="<?= $page->url('cart.service.delete', array('serviceId' => $service->getId(), 'productId' => $product->getId()));?>">Отменить
                    услугу</a>
            </div>
            <? endif; endforeach ?>
        <? } else { ?>
        <h3>Установка<br/>и подключение</h3>
        <? } ?>
        <a class="link1" href="">Выбрать услуги</a>
    </div>

<? endif ?>