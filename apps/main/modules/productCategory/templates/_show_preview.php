<div class="goodsbox height250">

  <div class="photo">
    <a href="<?php echo $order['url'] ?>"><!--<i class="new" title="Новинка"></i>--><img src="<?php echo $order['photo'] ?>" alt="<?php echo $order['name'] ?> - <?php echo $order['root_name'] ?>" title="<?php echo $order['name'] ?> - <?php echo $order['root_name'] ?>" width="160" height="160" /></a>
  </div>

  <h2><a href="<?php echo $order['url'] ?>" class="underline"><?php echo $order['name'] ?></a></h2>
  <ul>
	<?php foreach ($order['links'] as $link): ?>
    <li><a href="<?php echo $link['url'] ?>"><?php echo $link['name'] ?></a></li>
	<?php endforeach ?>
  </ul>
  <div class="font11"><a href="<?php echo $order['url'] ?>" class="underline gray"><?php echo $order['product_quantity'] ?> товаров</a></div>

  <!-- Hover -->
  <div class="boxhover">
    <b class="rt"></b><b class="lb"></b>
    <div class="rb">
      <div class="lt" data-url="<?php echo $order['url'] ?>">

        <div class="photo">
          <a href="<?php echo $order['url'] ?>"><!--<i class="new" title="Новинка"></i>--><img src="<?php echo $order['photo'] ?>" alt="<?php echo $order['name'] ?> - <?php echo $order['root_name'] ?>" title="<?php echo $order['name'] ?> - <?php echo $order['root_name'] ?>" width="160" height="160" /></a>
        </div>

        <h2><a href="<?php echo $order['url'] ?>" class="underline"><?php echo $order['name'] ?></a></h2>
        <ul>
          <?php foreach ($order['links'] as $link): ?>
		  <li><a href="<?php echo $link['url'] ?>"><?php echo $link['name'] ?></a></li>
		  <?php endforeach ?>
        </ul>
        <div class="font11"><a href="<?php echo $order['url'] ?>" class="underline gray"><?php echo $order['product_quantity'] ?> товаров</a></div>

      </div>
    </div>
  </div>
  <!-- /Hover -->
</div>
