<div class="goodsbox height250"<?php echo (isset($ii) && $ii > 3) ? ' style="display:none;"' : '' ?>>
  <div class="photo">
    <a href="<?php echo $order['url'] ?>">
      <?php if ($order['label']): ?>
      <img class="bLabels" src="<?php echo $order['label']->getImageUrl() ?>"
           alt="<?php echo $order['label']->getName() ?>"/>
      <?php endif ?>
      <img src="<?php echo $order['photo'] ?>" alt="Серия <?php echo $order['Line']['name'] ?>"
           title="Серия <?php echo $order['Line']['name'] ?>" width="160" height="160"/>
    </a>
  </div>
  <h3><a
    href="<?php echo $order['url'] ?>"><strong>Серия <?php echo $order['Line']['name'] . '</strong> <span class="font10 gray">(' . $order['Line']['count'] . ')</span>' ?>
  </a></h3>

  <!-- Hover -->
  <div class="boxhover"<?php if ($item['is_insale']): ?> ref="<?php echo $item['token'] ?>"<?php endif ?>>
    <b class="rt"></b><b class="lb"></b>

    <div class="rb">
      <div class="lt" data-url="<?php echo $order['url'] ?>">
        <!--a href="" class="fastview">Быстрый просмотр</a-->

        <div class="photo"><!--<i class="new" title="Новинка"></i>-->
          <a href="<?php echo $order['url'] ?>">
            <?php if ($order['label']): ?>
            <img class="bLabels" src="<?php echo $order['label']->getImageUrl() ?>"
                 alt="<?php echo $order['label']->getName() ?>"/>
            <?php endif ?>
            <img src="<?php echo $order['photo'] ?>" alt="Серия <?php echo $order['Line']['name'] ?>"
                 title="Серия <?php echo $order['Line']['name'] ?>" width="160" height="160"/>
          </a>
        </div>
        <h3><a
          href="<?php echo $order['url'] ?>"><strong>Серия <?php echo $order['Line']['name'] . '</strong> <span class="font10 gray">(' . $order['Line']['count'] . ')</span>' ?>
        </a></h3>
      </div>
    </div>
  </div>
  <!-- /Hover -->

</div>