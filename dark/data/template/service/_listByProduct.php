<?php
/**
 * @var $page \View\DefaultLayout
 * @var $product \Model\Product\Entity
 */

$list = $product->getService();
$listInCart = array();//$product->getServiceListInCart();
?>
<?php if (count($list)): ?>

    <?php echo $page->render('service/_selection', array('product' => $product)) ?>

<div class="bF1Info bBlueButton">
    <img class="bF1Info_Logo" src="/images/f1info.png" alt="Улуги F1" />
    <script type="text/html" id="f1look">
        <div ref="<%=fid%>">
            <%=f1title%> - <%=f1price%>&nbsp;
            <span class="rubl"> p</span>
            <br>
            <a class="bBacketServ__eMore"
               href="<?php echo $page->url('cart.service_delete', array('serviceId' => 'F1ID', 'productId' => $product->getId()));?>">Отменить услугу</a>
        </div>
    </script>
    <?php if (count($listInCart)) { ?>
    <h3>Вы добавили услуги:</h3>
    <?php foreach ($listInCart as $service): ?>
        <div ref="<?php echo $service->getToken();?>">
            <?php echo $service->getName() ?> - <?php echo formatPrice($service->getPrice()) ?>&nbsp;<span class="rubl">p</span><br>
            <a class="bBacketServ__eMore"
               href="<?php echo url_for('cart.service_delete', array('serviceId' => $service->getId(), 'productId' => $product->getId()));?>">Отменить услугу</a>
        </div>
        <?php endforeach ?>
    <?php } else { ?>
    <h3>Установка<br />и подключение</h3>
    <?php } ?>
    <a class="link1" href="">Выбрать услуги</a>
</div>

<?php endif ?>
