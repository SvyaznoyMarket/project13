<?php
/**
 * @var $item ProductEntity
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
    <h3>Выбирай услуги F1<br> вместе с этим товаром</h3>
    <?php } ?>
    <a class="link1" href="">Выбрать услуги</a>
  </div>

<?php endif ?>
