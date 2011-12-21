<div class="basketleft">
  <a href="<?php echo $item['url'] ?>"><img src="<?php echo $item['photo'] ?>" /></a>
</div>

<div class="basketright">
  <div class="goodstitle">
    <div class="font24 pb5"><a href=""><?php echo $item['name'] ?></a></div>
    <div class="font11"><?php if ($item['is_instock']) echo 'Есть в наличии' ?></div>
  </div>
  <div class="basketinfo pb15">
    <div class="left font11">Цена:<br>
    	<span class="font12">
    	<span class="c1price"><?php echo $item['price'] ?></span> <span class="rubl">p</span></span>
    </div>
    <div class="right"><div class="numerbox">
		<b class="c1less" title="Уменьшить"></b>
		<span class="c1quant">1 шт.</span>
		<b class="c1more" title="Увеличить"></b>
    </div></div>
  </div>
</div>