<?php
/**
 * @var $item ProductEntity
 * @var $warranty light\WarrantyCartData
 */
$list = $item->getServiceList();
$listInCart = $item->getServiceListInCart();
?>
<?php if (count($list)): ?>
  <?php render_partial('product_/templates/_f1_lightbox.php', array('item' => $item))?>

  <div class="bF1Info bBlueButton">
    <img class="bF1Info_Logo" src="/images/f1info.png" alt="Улуги F1" />
    <script type="text/html" id="f1look">
      <div ref="<%=fid%>">
        <%=f1title%> - <%=f1price%>&nbsp;
        <span class="rubl"> p</span>
        <br>
        <a class="bBacketServ__eMore"
           href="<?php echo url_for('cart_service_delete', array('service' => 'F1ID', 'product' => $item->getId()));?>">Отменить услугу</a>
      </div>
    </script>
    <?php if (count($listInCart)) { ?>
      <h3>Вы добавили услуги:</h3>
      <?php foreach ($listInCart as $service): ?>
        <div ref="<?php echo $service->getToken();?>">
          <?php echo $service->getName() ?> - <?php echo formatPrice($service->getPrice()) ?>&nbsp;<span class="rubl">p</span><br>
          <a class="bBacketServ__eMore"
             href="<?php echo url_for('cart_service_delete', array('service' => $service->getId(), 'product' => $item->getId()));?>">Отменить услугу</a>
        </div>
      <?php endforeach ?>
    <?php } else { ?>
    <h3>Установка<br />и подключение</h3>
    <?php } ?>
    <a class="link1" href="">Выбрать услуги</a>
  </div>

<?php endif ?>


<?php $warrantiesById = array(); foreach ($item->getWarrantyList() as $warranty) { $warrantiesById[$warranty->getId()] = $warranty; } ?>

<?php if ((bool)$warrantiesById): ?>
  <?php
    /** @var $user myUser */
    $user = sfContext::getInstance()->getUser()
  ?>

  <?php render_partial('product_/templates/_ext_warranty_lightbox.php', array('item' => $item))?>

  <div class="bBlueButton extWarranty">
    <img alt="Дополнительная гарантия" class="bF1Info_Logo" src="/images/F1_logo_extWarranty.jpg">
    <?php if ($warranty = $user->getCart()->getWarrantyByProduct($item->getId())) { ?>
      <h3>Вы выбрали гарантию:</h3>
      <div ref="<?php echo $warranty->getWarrantyId() ?>">
        <?php echo $warrantiesById[$warranty->getWarrantyId()]->getName() ?> - <?php echo formatPrice($warranty->getPrice()) ?>&nbsp;<span class="rubl">p</span><br>
          <a class="bBacketServ__eMore" href="<?php echo url_for('cart_warranty_delete', array('warranty' => $warranty->getWarrantyId(), 'product' => $item->getId()));?>">Отменить услугу</a>
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