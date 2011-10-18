<?php
$photos = $product->getAllPhotos();
$p3d = $product->getAll3dPhotos();
$urls = sfConfig::get('app_product_photo_url');
$urls3d = sfConfig::get('app_product_photo_3d_url');
$p3d_res_small = array();
$p3d_res_big = array();
foreach ($p3d as $p3d_obj)
{
  $p3d_res_small[] = $urls3d[0].$p3d_obj->resource;
  $p3d_res_big[] = $urls3d[1].$p3d_obj->resource;
}
?>
<script type="text/javascript">
  product_3d_small = <?php echo json_encode($p3d_res_small) ?>;
  product_3d_big = <?php echo json_encode($p3d_res_big) ?>;
</script>
<div class="goodsphoto"><!--i class="bestseller"></i--><a href="#" onclick="return false"><img src="<?php echo $product->getMainPhotoUrl(3) ?>" alt="" width="500" height="500" title="" /></a></div>
<div style="display:none;" id="stock">
  <!-- list of images 500*500 for preview -->
  <?php foreach ($photos as $i => $photo): ?>
    <img src="<?php echo $urls[4].$photo->resource ?>" alt="" ref="photo<?php echo $i ?>" width="500" height="500" title="" />
<?php endforeach ?>
</div>

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
      <a href="<?php //echo url_for('userDelayedProduct_create', $sf_data->getRaw('product'))  ?>#" class="link2"></a>
      <a href="<?php //echo url_for('userProductCompare_add', $sf_data->getRaw('product'))  ?>#" class="link3"></a>
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
    echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position-x:100%;"></span>', 5 - round($product->rating));
    ?>
    <strong class="ml5"><?php echo round($product->rating, 1) ?></strong>
<?php //include_component('userProductRating', 'show', array('product' => $product))  ?>
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
<?php include_component('product', 'product_group', array('product' => $product,)) ?>
  <ul class="inline">
    <li><?php include_component('cart', 'buy_button', array('product' => $product, 'quantity' => 1)) ?></li>
    <li><?php include_component('userDelayedProduct', 'add_button', array('product' => $product)) ?></li>
    <li><?php include_component('userProductCompare', 'button', array('product' => $product)) ?></li>
  </ul>

  <!--div class="inline">
<?php //include_component('userProductRating', 'show', array('product' => $product))  ?>
  </div->

  <div class="inline">
<?php //include_component('userTag', 'product_link', array('product' => $product))  ?>
  </div>

  <div class="block">
<?php //echo link_to('Следить за этим товаром', 'userProductNotice_show', $sf_data->getRaw('product'), array('class' => 'event-click', 'data-event' => 'window.open'))  ?>
  </div-->

</div><!-- Goods info -->

<div class="clear"></div>

<!-- Photo video -->
<?php if (count($p3d) > 0 || count($photos) > 0): ?>
  <div class="fl width500">
    <h2>Фото и видео товара:</h2>
    <div class="font11 gray pb10">Всего фотографий <?php echo count($photos) ?></div>
    <ul class="previewlist">
      <!-- IVN '.viewme' for opening in the popup; @ref='image'/'360' is a type   -->
      <?php foreach ($photos as $i => $photo): ?>
        <li class="viewstock" ref="photo<?php echo $i ?>"><b><a href="<?php echo $urls[4].$photo->resource ?>" class="viewme" ref="image" id="try-3"></a></b><img src="<?php echo $urls[2].$photo->resource ?>" alt="" width="48" height="48" /></li>
      <?php endforeach ?>
      <?php if (count($p3d) > 0): ?>
        <li><a href="#" class="axonometric viewme" ref="360" title="Объемное изображение">Объемное изображение</a></li>
  <?php endif ?>
    </ul>
  </div>
<?php endif ?>
<!-- /Photo video -->
<div class="clear"></div>

<!-- Description -->
<h2 class="bold">Характеристики</h2>
<div class="line pb25"></div>

<div class="descriptionlist">
<?php include_component('product', 'property_grouped', array('product' => $product)) ?>
</div>

<!--div class="pb25"><a href="" class="more">Все характеристики</a></div-->
<!-- /Description -->

<!-- Media -->
<div class="popup mediablock" id="bigpopup"><!-- IVN block #bigpopup is media gallery popup  -->
  <i title="Закрыть" class="close">Закрыть</i>

  <div class="float100">
    <div class="photoview">
      <div class="photobox" id="photobox"></div>
      <div class="scrollbox"><div><b></b></div></div>
    </div>
  </div>


  <div class="leftpanel" style="margin-left:-100%">
    <div class="topblock font16">
      <div class="logobox">Enter связной</div>
      <div class="pb5"><?php echo $item['product']->name ?></div>
      <div class="pb5">
<?php include_partial('product/price', array('price' => $product->getFormattedPrice())) ?>
      </div>
      <!--div class="pb40"><input type="button" class="button yellowbutton" value="Купить" /></div-->

      <h2>Фото и видео:</h2>
      <ul class="previewlist">
        <?php foreach ($photos as $i => $photo): ?>
          <li class="viewstock" ref="photo<?php echo $i ?>"><b><a href="<?php echo $urls[4].$photo->resource ?>" class="viewme" ref="image" id="try-3"></a></b><img src="<?php echo $urls[2].$photo->resource ?>" alt="" width="48" height="48" /></li>
        <?php endforeach ?>
        <?php if (count($p3d) > 0): ?>
          <li><a href="#" class="axonometric viewme" ref="360" title="Объемное изображение">Объемное изображение</a></li>
<?php endif ?>
      <!--li><b><a href="images/photo25_2.jpg" class="viewme" ref="image"></a></b><img src="images/photo27.jpg" alt="" width="48" height="48" /></li>
      <li><b><a href="images/photo25_3.jpg" class="viewme" ref="image"></a></b><img src="images/photo28.jpg" alt="" width="48" height="48" /></li>
      <li><b><a href=""></a></b><img src="images/photo26.jpg" alt="" width="48" height="48" /></li>
      <li><a href="" class="axonometric viewme" ref="360" title="Объемное изображение">Объемное изображение</a></li>
      <li><a href="" class="videolink" title="Видео">Видео</a></li-->
      </ul>
    </div>

    <!--div class="pb5"><a href="" class="share">Поделиться</a> <strong><a href="" class="nodecor">+87</a></strong></div>
    <div class="font9 gray">
      Дата съемок: Сентябрь 2010.<br />
      Производитель на свое усмотрение и
      без дополнительных уведомлений может
      менять комплектацию, внешний вид и
      технические характеристики модели.
      Данные фото могут не соответствовать
      новым изменениям и дополнениям.<br>
      &copy; 2011–2012. Все права защищены и
      принадлежат "Enter.ru".
      Копирование и распространение
      материалов в любой форме запрещены
    </div-->
  </div>


  <div class="scalepanel" style="float:left; margin-left:-80px">
    <div class="zoom"></div>

    <div class="scale">
      <b class="plus"></b>
      <div class=""><b class="zoomind" style="top:45%"></b></div>
      <b class="minus"></b>
    </div>

    <div class="versioncontrol">
      <div class="pb5 gray" id="percents" style="font-size:150%;">0%</div>
      <div id="turnlite">
        <div class="font9 gray">Нет времени ждать загрузки? <br/>Загрузи</div>
        <div class="pb5 orange">легкую версию</div>
      </div>
      <div class="pb5 orange" id="turnfull" style="display:none;">полная версия</div>
    </div>
  </div>
</div>
<!-- /Media -->