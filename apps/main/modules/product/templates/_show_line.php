<div class="goodsbox height250"<?php echo (isset($ii) && $ii > 3) ? ' style="display:none;"' : '' ?>>
  <div class="photo"><!--<i class="new" title="Новинка"></i>-->
  	<a href="<?php echo $item['url'] ?>"><img src="<?php echo $item['photo'] ?>" alt="Серия <?php echo $item['product']->Line->name ?>" title="Серия <?php echo $item['product']->Line->name ?>" width="160" height="160" /></a>
  </div>
  <h3><a href="<?php echo $item['url'] ?>"><strong>Серия <?php echo $item['product']->Line->name.'</strong> <span class="font10 gray">('.$item['product']->Line->getCount().')</span>' ?></a></h3>

  <!-- Hover -->
  <div class="boxhover"<?php if ($item['product']->getIsInsale()):?> ref="<?php echo $item['product']->token ?>"<?php endif ?>>
    <b class="rt"></b><b class="lb"></b>
    <div class="rb">
      <div class="lt" data-url="<?php echo $item['url'] ?>">
        <!--a href="" class="fastview">Быстрый просмотр</a-->

        <div class="photo"><!--<i class="new" title="Новинка"></i>-->
        <a href="<?php echo $item['url'] ?>"><img src="<?php echo $item['photo'] ?>" alt="Серия <?php echo $item['product']->Line->name ?>" title="Серия <?php echo $item['product']->Line->name ?>" width="160" height="160" /></a>
        </div>
        <h3><a href="<?php echo $item['url'] ?>"><strong>Серия <?php echo $item['product']->Line->name.'</strong> <span class="font10 gray">('.$item['product']->Line->getCount().')</span>' ?></a></h3>
      </div>
    </div>
  </div>
  <!-- /Hover -->

</div>