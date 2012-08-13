<div class='bShopCard' onclick="window.location='<?php echo $order['url'] ?>'">
  <img class='bShopCard__eIco' src='<?php if ($order['main_photo']) echo $order['main_photo']['url_small'] ?>' width="162" height="100">
  <h3 class='bShopCard__eTitle'><?php echo $order['name'] ?></h3>
  <?php if ($order['is_reconstruction']): ?>
  <p class='bShopCard__eDescription red'>На реконструкции</p>
  <?php elseif ($order['regime']) :?>
  <p class='bShopCard__eDescription'><?php echo 'Работаем '.$order['regime'] ?></p>
  <?php endif ?>

  <a href="<?php echo $order['url'] ?>" class="bShopCard__eView">Подробнее о магазине</a>
</div>