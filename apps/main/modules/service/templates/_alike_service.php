<?php if (count($list)): ?>
  <div class="rubrictitle"><h3>А так же есть похожие услуги:</h3></div>
  <div class="line pb15"></div>
  <div class="clear"></div>

  <div class="bServiceCardWrap">

    <?php $num = 0; foreach ($list as $service): ?>

      <div class="bServiceCard mInlineBlock" ref="<?php echo $service['token'] ?>">
        <div class="bServiceCard__eImage">
          <?php if ($service['photo']): ?>
            <div class="bServiceCard__eLogo"></div>
            <a href="<?php echo url_for('service_show', array('service' => $service['token'])) ?>">
              <img src="<?php echo $service['photo']; ?>">
            </a>
          <?php else: ?>
            <a href="<?php echo url_for('service_show', array('service' => $service['token'])) ?>">
              <div class="bServiceCard__eLogo_free"></div>
            </a>
          <?php endif ?>
        </div>

        <p class="bServiceCard__eDescription mb5">
          <a href="<?php echo url_for('service_show', array('service' => $service['token'])) ?>">
            <?php echo $service['name']; ?>
          </a>
        </p>

        <div class="bServiceCard__ePrice pb10">
          <?php if ($showNoPrice || $service['price'] != 'бесплатно'): ?>
            <?php echo ($service['price'] < 1) ? 'бесплатно' : $service['price'] ?>

            <?php if ((int)$service['price']): ?><span class="rubl">p</span><?php endif ?>
          <?php endif ?>

          <?php if ($service['only_inshop']): ?>
            <div>доступна в магазине</div>
          <?php endif ?>

        </div>

        <?php $json = array(
          'jsref'   => $service['token'],
          'jsimg'   => $service['photo'],
          'jstitle' => $service['name'],
          'jsprice' => $service['priceFormatted'],
          'url'     => url_for('cart_service_add', array('service' => $service['token'])),
        ) ?>

        <?php if (!$service['only_inshop']): ?>
        <form action="<?php echo url_for('cart_service_add', array('service' => $service['token'])) ?>"/>
        <input data-value='<?php echo json_encode($json) ?>' type="submit" class="button yellowbutton" value="Купить услугу">
        </form>
        <?php endif ?>


      </div>
      <?php $num++; if ($num >= 4) break ?>

    <?php endforeach ?>

  </div>
<?php endif ?>
