<?php if (count($list)): ?>
<?php
  include_component('productSoa', 'f1_lightbox', array('f1' => $list, 'product' => $product, 'servListId' => $servListId, 'parentAction' => $this->getActionName()))
  ?>

<div class="bF1Info bBlueButton">
  <img class="bF1Info_Logo" src="/images/f1info.png" alt="Улуги F1" />
  <script type="text/html" id="f1look">
    <div ref="<%=fid%>">
      <%=f1title%> - <%=f1price%>&nbsp;
      <span class="rubl"> p</span>
      <br>
      <a class="bBacketServ__eMore"
         href="<?php echo url_for('cart_service_delete', array('service' => 'F1ID', 'product' => $product->id));?>">Отменить
        услугу</a>
    </div>
  </script>
  <?php if (count($servListId)) { ?>
  <h3>Вы добавили услуги:</h3>
  <?php
  foreach ($list as $service) {
    ?>
    <?php if (in_array($service['id'], $servListId)) { ?>
      <div ref="<?php echo $service['token'];?>">
        <?php echo $service['name'] ?> - <?php echo $service['price']  ?>&nbsp;<span class="rubl">p</span><br>
        <a class="bBacketServ__eMore"
           href="<?php echo url_for('cart_service_delete', array('service' => $service['id'], 'product' => $product->id));?>">Отменить
          услугу</a>
      </div>
      <?php } ?>
    <?php } ?>

  <?php } else { ?>
  <h3>Выбирай услуги F1<br> вместе с этим товаром</h3>
  <?php } ?>
  <a class="link1" href="">Выбрать услуги</a>
</div>

<?php endif ?>
