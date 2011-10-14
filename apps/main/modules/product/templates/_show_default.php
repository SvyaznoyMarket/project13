<div class="goodsphoto"><i class="bestseller"></i><a href=""><img src="<?php echo $item['photo'] ?>" alt="" width="300" height="300" title="" /></a></div>
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
	<script type="text/javascript">
	$('#watch-trigger').click(function(){
		$('#watch-cnt').toggle();
	});
	$('#watch-cnt .close').click(function(){
		$('#watch-cnt').hide();
	});
	</script>
        <div class="font14 pb15"><?php echo $item['product']->tagline ?></div>
        <div class="clear"></div>

        <div class="fl pb15">
			<div class="font10"><br/><br/></div>
<!--            <div class="font10">Старая цена<br /><span class="through">33 990 <span class="rubl">&#8399;</span></span></div>-->
            <div class="pb10"><?php include_partial('product/price', array('price' => $product->getFormattedPrice())) ?></div>
            <?php if ($product->is_instock): ?>
			<div class="pb5"><strong class="orange">Есть в наличии</strong></div>
			<?php endif ?>
<!--            <div class="pb3"><strong>Доставка стандарт</strong></div>
            <div class="font11 gray">
                Стоимость: <strong>350 руб.</strong><br />
                Москва. Доставим в течение 1-2 дней<br />
                <a href="" class="underline">Хотите быстрее?</a>
            </div>-->
        </div>
        <div class="fr ar pb15">
            <div class="goodsbarbig">
                <a href="<?php echo url_for('cart_add', array('product' => $product->token, 'quantity' => 1)) ?>" class="link1"></a>
                <a href="<?php //echo url_for('userDelayedProduct_create', $sf_data->getRaw('product')) ?>#" class="link2"></a>
                <a href="<?php //echo url_for('userProductCompare_add', $sf_data->getRaw('product')) ?>#" class="link3"></a>
            </div>
<!--            <div class="pb5"><strong><a href="" class="red underline">Купить быстро в 1 клик</a></strong></div>-->
<!--            <a href="<?php echo url_for('productStock', $sf_data->getRaw('product')) ?>" class="underline">Где купить в магазинах?</a>-->
        </div>

        <div class="line pb15"></div>

        <div class="pb5"><a href="<?php echo url_for('productComment', $sf_data->getRaw('product')) ?>" class="underline">Читать отзывы</a> (<?php echo $product->getCommentCount() ?>)</div>
        <div class="pb5">
			Оценка пользователей:
			<?php
				echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($product->rating));
				echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position-x:100%;"></span>', 5-round($product->rating));
			?>
			<strong class="ml5"><?php echo round($product->rating, 1) ?></strong>
			<?php //include_component('userProductRating', 'show', array('product' => $product)) ?>
		</div>
<!--        <div class="pb5">Понравилось? <a href="" class="share">Поделиться</a> <strong><a href="" class="nodecor">+87</a></strong></div>-->
        <div class="pb3"><?php include_component('userTag', 'product_link', array('product' => $product)) ?></div>

		<?php $f1 = $product->getServiceList() ?>
		<?php if (count($f1)): ?>
        <div class="f1links form">
            <div class="f1linkbox">
                <a href="" class="f1link">Сервис F1</a> Сервис F1
            </div>
            <div class="f1linkslist">
                <ul>
					<?php foreach ($f1 as $service): ?>
                    <li><label for="checkbox-<?php echo $service->id ?>"><?php echo $service->name ?> (<?php echo $service->getPriceByRegion($sf_user->getRegion()) ?> Р)</label><input id="checkbox-<?php echo $service->id ?>" name="service[<?php echo $service->id ?>]" type="checkbox" value="1" /></li>
					<?php endforeach ?>
                </ul>
                <a href="#" class="underline">подробнее</a>
            </div>
        </div>
		<?php endif ?>

        <div class="line pb15"></div>

<?php echo $product->Creator ?>
<?php include_component('product', 'product_group', array('product' => $product, )) ?>
<ul class="inline">
  <li><?php include_component('cart', 'buy_button', array('product' => $product, 'quantity' => 1)) ?></li>
  <li><?php include_component('userDelayedProduct', 'add_button', array('product' => $product)) ?></li>
  <li><?php include_component('userProductCompare', 'button', array('product' => $product)) ?></li>
</ul>

  <!--div class="inline">
  <?php //include_component('userProductRating', 'show', array('product' => $product)) ?>
  </div->

  <div class="inline">
  <?php //include_component('userTag', 'product_link', array('product' => $product)) ?>
  </div>

  <div class="block">
  <?php //echo link_to('Следить за этим товаром', 'userProductNotice_show', $sf_data->getRaw('product'), array('class' => 'event-click', 'data-event' => 'window.open')) ?>
  </div-->

</div><!-- Goods info -->

<div class="clear"></div>

<!-- Description -->
<h2 class="bold">Характеристики</h2>
<div class="line pb25"></div>

<div class="descriptionlist">
  <?php include_component('product', 'property_grouped', array('product' => $product)) ?>
</div>

    <!--div class="pb25"><a href="" class="more">Все характеристики</a></div-->
    <!-- /Description -->