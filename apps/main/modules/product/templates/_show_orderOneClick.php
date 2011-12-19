<div class="basketleft">
  <a href="<?php echo $item['url'] ?>"><img src="<?php echo $item['photo'] ?>" /></a>
</div>

<div class="basketright">
  <div class="goodstitle">
    <div class="font24 pb5"><a href=""><?php echo $item['name'] ?></a></div>
    <div class="font11"><?php if ($item['is_instock']) echo 'Есть в наличии' ?></div>
  </div>
  <div class="basketinfo pb15">
    <div class="left font11">Цена:<br><span class="font12"><?php echo $item['price'] ?> <span class="rubl">p</span></span></div>
    <div class="right"><div class="numerbox"><b title="Уменьшить"></b><span>1 шт.</span><b title="Увеличить"></b></div></div>
  </div>
</div>