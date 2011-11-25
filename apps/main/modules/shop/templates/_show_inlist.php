<div class='bShopCard' onclick="window.location='<?php echo $item['url'] ?>'">
  <img class='bShopCard__eIco' src='<?php if ($item['main_photo']) echo $item['main_photo']['url_small'] ?>' width="162" height="100">
  <h3 class='bShopCard__eTitle'><?php echo $item['name'] ?></h3>
  <p class='bShopCard__eDescription'><?php if ($item['regime']) echo 'Работаем '.$item['regime'] ?></p>

  <a href="<?php echo $item['url'] ?>" class="bShopCard__eView">Подробнее о магазине</a>
</div>