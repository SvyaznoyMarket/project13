<div class="bDeliver2">
  <h4>Как получить заказ?</h4>
  <ul>
    <li>
      <?php
      if (!count($delivery)) {
        ?>
        <h5>Можно заказать сейчас с доставкой</h5>
        <div>&mdash; Можем доставить <?php echo myToolkit::formatDeliveryDate(2); ?></div>
        <?php
      } else {
        foreach ($delivery as $info) {
          if ($info['price'] > 0) {
            $price = $info['price'] . ' руб.';
          } else {
            $price = 'бесплатно.';
          }
          $deliveryText = str_replace(array('сегодня', 'завтра'), array('<b>сегодня</b>', '<b>завтра</b>'), $info['deliveryText']);
          if ($info['mode'] == 1) {
            ?>
            <h5>Можно заказать сейчас с доставкой</h5>
            <div> &mdash; Можем доставить <?php echo $deliveryText ?>, <?php echo $price ?></div>
            <?php } elseif ($info['mode'] == 2) { ?>
            <h5>Можно заказать сейчас с доставкой</h5>
            <div> &mdash; Можем доставить <?php echo $deliveryText ?>, <?php echo $price ?></div>
            <?php } elseif ($info['mode'] == 3) { ?>
            <h5>Можно заказать сейчас и самостоятельно забрать в магазине <?php echo $deliveryText ?></h5>
            <div>&mdash; <a target="blank"
                            href="<?php echo url_for('productStock', array('product' => $product->path)) ?>">В каких
              магазинах ENTER можно забрать?</a></div>
            <?php
          }
        }
      }
      ?>
    </li>
  </ul>
</div>
<div class="line pb15"></div>