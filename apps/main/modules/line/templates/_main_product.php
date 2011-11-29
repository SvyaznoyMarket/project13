    <h2 class="mbSet"><strong><?php echo $item['name'] ?></strong></h2>
    <div class="line pb15"></div>
	
	<div class='bSet'>
		<div class='bSet__eImage'>
			<a href="<?php echo $item['url'] ?>"><img src="<?php echo $item['photo'] ?>" alt="" width="350" height="350" title="" /></a>
		</div>
		<div class='bSet__eInfo'>
			<div class='bSet__eArticul'>Артикул #<?php echo $item['product']->article ?></div>
			<p class='bSet__eDescription'><?php echo $item['description'] ?></p>
			<div class='bSet__ePrice'>
				<span class="fl mr5"><?php include_partial('product/price', array('price' => $item['product']->getFormattedPrice(), 'noClasses' => true, )) ?></span>
                                <span class="fl"><?php include_component('cart', 'buy_button', array('product' => $item['product'], 'quantity' => 1, 'value' => array('Купить набор'),)) ?></span>
                                <div class="clear"></div>
			</div>
			<div class='bSet__eIconsWrap'>
				<h3 class='bSet__eG'>Состав набора:</h3>
				<div class='bSet__eIcons'>
					<ul class="previewlist">
                                            <?php foreach ($item['part'] as $part): ?>
        			            <li><b><a href="<?php echo $part['url'] ?>" title="<?php echo $item['name'] ?>"></a></b><img src="<?php echo $part['photo'] ?>" alt="<?php echo $item['name'] ?>" width="48" height="48"></li>
                                            <?php endforeach ?>
			        </ul>
				</div>
				<div class='bSet__eTWrap'><a class='bSet__eMoreInfo' href="<?php echo $item['url'] ?>">Подробнее о наборе</a></div>
			</div>
		</div>
	</div>
 
<?php if (false): ?>
<div class="goodsphoto"><!--i class="bestseller"></i--><a href="<?php echo $item['url'] ?>"><img src="<?php echo $item['photo'] ?>" alt="" width="500" height="500" title="" /></a></div>

<div class="goodsinfo"><!-- Goods info -->
  <div class="article">
    <!--            <div class="fr"><a href="javascript:void()" id="watch-trigger">Следить за товаром</a> <a href="" rel="nofollow">Печать</a></div>-->
    Артикул #<?php echo $item['product']->article ?>

    <!-- Watch -->
    <div class="hideblock width358" id="watch-cnt">
      <i title="Закрыть" class="close">Закрыть</i>
      <div class="title">Получать сообщения</div>
      <form action="" class="form">
        <ul class="checkboxlist pb10">
          <li><label for="checkbox-7">когда снизится цена</label><input id="checkbox-7" name="checkbox-3" type="checkbox" value="checkbox-1" /></li>
          <li><label for="checkbox-8">когда появится новый отзыв </label><input id="checkbox-8" name="checkbox-3" type="checkbox" value="checkbox-2" /></li>
          <li><label for="checkbox-9">когда товар появится в магазинах сети</label><input id="checkbox-9" name="checkbox-3" type="checkbox" value="checkbox-3" /></li>
        </ul>
        <div class="pb5">Ваш E-mail:</div>
        <input type="text" class="text width181 mb10" value="user@mail.ru" />
        <div class="pb20"><input type="button" class="yellowbutton yellowbutton106" value="Подтверждаю" /></div>
        <div class="font11 gray">Внимание!<br />Вы всегда сможете отписаться от данной рассылки в самой рассылкеили в личном кабинете</div>
      </form>
    </div>
    <!-- /Watch -->

  </div>
  <div class="font14 pb15"><?php echo $item['description'] ?></div>
  <div class="clear"></div>

  <div class="fl pb15">
    <div class="font10"><br/><br/></div>
    <div class="pb10"><?php include_partial('product/price', array('price' => $item['product']->getFormattedPrice())) ?></div>
    <?php if ($item['product']->is_instock): ?>
    <noindex><div class="pb5"><strong class="orange">Есть в наличии</strong></div></noindex>
    <?php endif ?>
  </div>
  <div class="fr ar pb15">
    <div class="goodsbarbig" ref="<?php echo $item['product']->token ?>">
      <?php echo include_component('cart', 'buy_button', array('product' => $item['product'], 'quantity' => 1)) ?>
<!--      <a href="<?php echo url_for('cart_add', array('product' => $item['product']->token, 'quantity' => 1)) ?>" class="link1"></a>-->
      <a href="<?php //echo url_for('userDelayedProduct_create', $sf_data->getRaw('product'))  ?>javascript:void()" class="link2"></a>
      <a href="<?php //echo url_for('userProductCompare_add', $sf_data->getRaw('product'))  ?>javascript:void()" class="link3"></a>
    </div>
<!--            <div class="pb5"><strong><a href="" class="red underline">Купить быстро в 1 клик</a></strong></div>-->
    <a href="<?php echo $item['shop_url'] ?>" class="underline">Где купить в магазинах?</a>
  </div>

  <div class="clear pb15"></div>
  <div class="mb15 font12 orange infoblock">
    Любой из представленных товаров, вы можете заказать с доставкой по удобному адресу.
    <br /><span class="black" style="line-height: 2;">Подробности по телефону 8 (800) 700 00 09</span>
  </div>
  <div class="pb5"><span id="rating" data-url="<?php echo url_for('userProductRating_createtotal', array('rating' => 'score', 'product' => $item['product']->token )) ?>">
    Оценка пользователей:
    <?php
    echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($item['product']->rating));
    echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($item['product']->rating));
    ?></span>
    <strong class="ml5"><?php echo round($item['product']->rating, 1) ?></strong>
    <?php //include_component('userProductRating', 'show', array('product' => $item['product']))  ?>
  </div>
<!--        <div class="pb5">Понравилось? <a href="" class="share">Поделиться</a> <strong><a href="" class="nodecor">+87</a></strong></div>-->
  <div class="pb3"><?php include_component('userTag', 'product_link', array('product' => $item['product'])) ?></div>

  <?php $f1 = $item['product']->getServiceList(); ?>
<?php
#print_r($f1->toArray());
if (count($f1)): 
    $num = 0;
    ?>
   <?php
    include_component('product', 'f1_lightbox', array('f1' => $f1,))  
   ?>
    <div class="f1links form">
      <div class="f1linkbox">
        <a href="" class="f1link">Сервис F1</a> Сервис F1
      </div>
      <div class="f1linkslist">
        <ul>
          <?php foreach ($f1 as $service):
                  if (!$service->getPriceByRegion()) continue;
              ?>
            <li>
                <label for="checkbox-small-<?php echo $service->id ?>">
                        <?php echo $service->name ?> (<?php echo (int)$service->getPriceByRegion() ?> Р)
                </label>
                <input 
                    <?php if (key_exists($service->id, $selectedServices)) echo 'checked="checked"'; ?>
                    id="checkbox-small-<?php echo $service->id ?>" name="service[<?php echo $service->id ?>]" type="checkbox" value="1" />
            </li>
        <?php
         $num++;
         if ($num==3) break;
         endforeach ?>
        </ul>
        <a href="#" class="underline">подробнее</a>
      </div>
    </div>
<?php endif ?>

  <div class="line pb15"></div>

</div><!-- Goods info -->

<div class="clear"></div>
<?php endif ?>