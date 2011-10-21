<div class="goodsbox">

  <div class="photo">
    <a href="<?php echo $item['url'] ?>"><!--<i class="new" title="Новинка"></i>--><img src="<?php echo $item['photo'] ?>" alt="" title="" width="160" height="160" /></a>
  </div>

  <h2><a href="<?php echo $item['url'] ?>" class="underline"><?php echo $item['name'] ?></a></h2>
  <ul>
	<?php foreach ($item['links'] as $link): ?>
    <li><a href="<?php echo $link['url'] ?>"><?php echo $link['name'] ?></a></li>
	<?php endforeach ?>
  </ul>
  <div class="font11"><a href="<?php echo $item['url'] ?>" class="underline gray"><?php echo $item['product_quantity'] ?> товаров</a></div>

  <!-- Hover -->
  <div class="boxhover">
    <b class="rt"></b><b class="lb"></b>
    <div class="rb">
      <div class="lt">

        <div class="photo">
          <a href="<?php echo $item['url'] ?>"><!--<i class="new" title="Новинка"></i>--><img src="<?php echo $item['photo'] ?>" alt="" title="" width="160" height="160" /></a>
        </div>

        <h2><a href="<?php echo $item['url'] ?>" class="underline"><?php echo $item['name'] ?></a></h2>
        <ul>
          <?php foreach ($item['links'] as $link): ?>
		  <li><a href="<?php echo $link['url'] ?>"><?php echo $link['name'] ?></a></li>
		  <?php endforeach ?>
        </ul>
        <div class="font11"><a href="<?php echo $item['url'] ?>" class="underline gray"><?php echo $item['product_quantity'] ?> товаров</a></div>

      </div>
    </div>
  </div>
  <!-- /Hover -->
</div>
