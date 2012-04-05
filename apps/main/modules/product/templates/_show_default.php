<?php
#JSON data
$json = array(
  'jsref' => $product->token,
  'jstitle' => htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'),
  'jsprice' => $item['price'],
  'jsimg' => $product->getMainPhotoUrl(3)
)
?>
<?php
$photos = $product->getAllPhotos();
$p3d = $product->getAll3dPhotos();
$urls = sfConfig::get('app_product_photo_url');
$urls3d = sfConfig::get('app_product_photo_3d_url');
$p3d_res_small = array();
$p3d_res_big = array();
foreach ($p3d as $p3d_obj)
{
  $p3d_res_small[] = $urls3d[0] . $p3d_obj->resource;
  $p3d_res_big[] = $urls3d[1] . $p3d_obj->resource;
}
?>
<script type="text/javascript">
  product_3d_small = <?php echo json_encode($p3d_res_small) ?>;
  product_3d_big = <?php echo json_encode($p3d_res_big) ?>;
</script>
<div class="goodsphoto">
  <a href="<?php echo $product->getMainPhotoUrl(4)  ?>" class="viewme" ref="image" onclick="return false">
    <?php if ($item['label']): ?>
    <img class="bLabels" src="<?php echo $item['label']->getImageUrl(1) ?>"
         alt="<?php echo $item['label']->getName() ?>"/>
    <?php endif ?>
    <img src="<?php echo $product->getMainPhotoUrl(3) ?>" alt="" width="500" height="500" title=""/>
  </a>
</div>
<div style="display:none;" id="stock">
  <!-- list of images 500*500 for preview -->
  <?php foreach ($photos as $i => $photo): ?>
  <img src="<?php echo $urls[3] . $photo->resource ?>" alt="" data-url="<?php echo $urls[4] . $photo->resource ?>"
       ref="photo<?php echo $i ?>" width="500" height="500" title=""/>
  <?php endforeach ?>
</div>

<!-- Goods info -->
<div class="goodsinfo bGood">
  <div class="bGood__eArticle">
    <div class="fr">
          <span id="rating"
                data-url="<?php echo url_for('userProductRating_createtotal', array('rating' => 'score', 'product' => $product['token_prefix'] . '/' . $product['token'],)) ?>"<?php if ($item['rated']) echo ' data-readonly="true"' ?>>
            <?php
            echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($item['rating']));
            echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($item['rating']));
            ?>
          </span>
      <strong class="ml5 hf"><?php echo round($product->rating, 1) ?></strong>


      <a href="<?php echo url_for('productComment', $sf_data->getRaw('product')) ?>" class="underline ml5">Читать
        отзывы</a> <span>(<?php echo $product->getCommentCount() ?>)</span>
    </div>
    <span>Артикул #<?php echo $item['article'] ?></span>
  </div>

  <div class="font14 pb15"><?php echo $item['preview'] ?></div>
  <div class="clear"></div>

  <?php if (!empty($item['label']) && $item['avg_price'] > 0 && $item['price'] < $item['avg_price']): ?>
  <div class="mOurGray">Цена не у
    нас<br><?php include_partial('product/price', array('price' => $item['avg_price'], 'noStrong' => true,)) ?></div>
  <div class="clear"></div>

  <div class="clear mOur pt10">Наша цена</div>
  <?php endif ?>
  <div class="fl pb15">
    <div class="pb10"><?php include_partial('product/price', array('price' => $item['price'])) ?></div>
    <?php if ($product['is_instock']): ?>
    <div class="pb5"><strong class="orange">Есть в наличии</strong></div>
    <?php endif ?>
  </div>
  <div class="fr ar pb15">
    <div class="goodsbarbig mSmallBtns" ref="<?php echo $item['token'] ?>"
         data-value='<?php echo json_encode($json) ?>'>

      <div class='bCountSet'>
        <?php if (!$item['cart_quantity']): ?>
        <a class='bCountSet__eP' href>+</a><a class='bCountSet__eM' href>-</a>
        <?php else: ?>
        <a class='bCountSet__eP disabled' href>&nbsp;</a><a class='bCountSet__eM disabled' href>&nbsp;</a>
        <?php endif ?>
        <span><?php echo $item['cart_quantity'] ? $item['cart_quantity'] : 1 ?> шт.</span>
      </div>

      <?php echo include_component('cart', 'buy_button', array('product' => $product, 'quantity' => 1)) ?>
    </div>
    <?php if (false && $item['is_insale'] && $sf_user->getRegion('region')->is_default): ?>
    <div class="pb5"><strong><a href="<?php echo url_for('order_1click', array('product' => $item['barcode'])) ?>"
                                class="red underline order1click-link">Купить быстро в 1 клик</a></strong></div>
    <?php endif ?>
  </div>


  <div class="line pb15"></div>

  <?php if ($item['is_insale']): ?>
  <div class="bDeliver2 delivery-info" id="product-id-<?php echo $item['core_id'] ?>"
       data-shoplink="<?php echo $item['stock_url'] ?>" data-calclink="<?php echo url_for('product_delivery') ?>">
    <h4>Как получить заказ?</h4>
    <ul>
      <li>
        <h5>Идет расчет условий доставки...</h5>
      </li>
    </ul>
  </div>
  <div class="line pb15"></div>
  <?php endif ?>




  <?php include_component('service', 'listByProduct', array('product' => $product)) ?>

  <?php if (false): ?>

  <div class='bF1Info bBlueButton'>
    <h3>Выбирай услуги F1<br> вместе с этим товаром</h3>
    <a href class='link1'>Выбрать услуги</a>
  </div>
  <?php endif ?>
</div>
<!-- /Goods info -->



<?php if (false): //старая версия ?>
<div class="goodsinfo"><!-- Goods info -->
  <h2 style="padding: 0;">Артикул #<?php echo $product->article ?></h2>

  <div class="line mb10"></div>
  <?php if (false): ?>
    <div class="article">
      <!--            <div class="fr"><a href="javascript:void()" id="watch-trigger">Следить за товаром</a> <a href="" rel="nofollow">Печать</a></div>-->
      Артикул #<?php echo $product->article ?>

      <!-- Watch -->
      <div class="hideblock width358" id="watch-cnt">
        <i title="Закрыть" class="close">Закрыть</i>

        <div class="title">Получать сообщения</div>
        <form action="" class="form">
          <ul class="checkboxlist pb10">
            <li><label for="checkbox-7">когда снизится цена</label><input id="checkbox-7" name="checkbox-3"
                                                                          type="checkbox" value="checkbox-1"/></li>
            <li><label for="checkbox-8">когда появится новый отзыв </label><input id="checkbox-8" name="checkbox-3"
                                                                                  type="checkbox" value="checkbox-2"/>
            </li>
            <li><label for="checkbox-9">когда товар появится в магазинах сети</label><input id="checkbox-9"
                                                                                            name="checkbox-3"
                                                                                            type="checkbox"
                                                                                            value="checkbox-3"/></li>
          </ul>
          <div class="pb5">Ваш E-mail:</div>
          <input type="text" class="text width181 mb10" value="user@mail.ru"/>

          <div class="pb20"><input type="button" class="yellowbutton yellowbutton106" value="Подтверждаю"/></div>
          <div class="font11 gray">Внимание!<br/>Вы всегда сможете отписаться от данной рассылки в самой рассылкеили в
            личном кабинете
          </div>
        </form>
      </div>
      <!-- /Watch -->

    </div>
    <?php endif ?>
  <div class="font14 pb15"><?php echo $product->preview ?></div>
  <div class="clear"></div>

  <div class="fl pb15">
    <div class="font10"><br/><br/></div>
    <div class="pb10"><?php include_partial('product/price', array('price' => $product->getFormattedPrice())) ?></div>
    <?php if ($product->is_instock): ?>
    <noindex>
      <div class="pb5"><strong class="orange">Есть в наличии</strong></div>
    </noindex>
    <?php endif ?>
    <?php if (false): ?>
    <div class="pb3"><strong>Доставка: <?php echo $delivery['name'] ?></strong></div>
    <div class="font11 gray">
      Стоимость: <strong><?php echo $deliveryData['price'] ?> руб.</strong><br/>
      <?php echo 'Доставка возможна ' . myToolkit::formatDeliveryDate($deliveryPeriod) ?>
      <!--      Москва. Доставим в течение 1-2 дней<br />
   <a href="" class="underline">Хотите быстрее?</a>-->
    </div>
    <?php endif ?>
  </div>


  <div class="fr ar pb15">
    <div class="goodsbarbig" ref="<?php echo $product->token ?>">
      <?php echo include_component('cart', 'buy_button', array('product' => $product, 'quantity' => 1)) ?>
      <!--      <a href="<?php echo url_for('cart_add', array('product' => $product->token_prefix . '/' . $product->token, 'quantity' => 1)) ?>" class="link1"></a>-->
      <a href="<?php //echo url_for('userDelayedProduct_create', $sf_data->getRaw('product'))  ?>javascript:void()"
         class="link2"></a>
      <a href="<?php //echo url_for('userProductCompare_add', $sf_data->getRaw('product'))  ?>javascript:void()"
         class="link3"></a>
    </div>

    <?php if (false): ?>
    <div class="pb5"><strong><a onClick="_gaq.push(['_trackEvent', 'QuickOrder', 'Open']);" id="1click-trigger"
                                href="<?php echo url_for('order_1click', array('product_id' => $product->id)) ?>"
                                class="red underline">Купить быстро в 1 клик</a></strong></div>
    <?php endif ?>

    <a href="<?php echo $item['stock_url'] ?>" class="underline">В каких магазинах ENTER можно купить?</a>
  </div>

  <div class="clear pb15"></div>
  <div class="mb15 font12 orange infoblock delivery-info" id="product-id-<?php echo $product->core_id ?>">
    <?php if (count($product->Category) && 'furniture' == $product->Category->getFirst()->getRootCategory()->token): ?>
    Этот товар вы можете заказать с доставкой по удобному адресу.
    <?php else: ?>
    Этот товар вы можете заказать с доставкой по удобному адресу или заказать и самостоятельно забрать в магазине.
    <?php endif; ?>
    <br/><a href="<?php echo url_for('default_show', array('page' => 'how_get_order',)) ?>" class="underline">Стоимость
    и условия доставки</a><br/><span class="black"
                                     style="line-height: 2;">Подробности по телефону 8 (800) 700 00 09</span>
  </div>
  <div class="pb5"><a href="<?php echo url_for('productComment', $sf_data->getRaw('product')) ?>" class="underline">Читать
    отзывы</a> (<?php echo $product->getCommentCount() ?>)
  </div>
  <div class="pb5"><span id="rating"
                         data-url="<?php echo url_for('userProductRating_createtotal', array('rating' => 'score', 'sf_subject' => $product)) ?>"<?php if ($item['rated']) echo ' data-readonly="true"' ?>>
    Оценка пользователей:
    <?php
    echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($product->rating));
    echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($product->rating));
    ?></span>
    <strong class="ml5"><?php echo round($product->rating, 1) ?></strong>
    <?php //include_component('userProductRating', 'show', array('product' => $product))  ?>
  </div>
  <!--        <div class="pb5">Понравилось? <a href="" class="share">Поделиться</a> <strong><a href="" class="nodecor">+87</a></strong></div>-->
  <div class="pb3"><?php include_component('userTag', 'product_link', array('product' => $product)) ?></div>


  <div class="line pb15"></div>

  <?php //echo $product->Creator ?>
  <?php if (false): ?>1
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
    <?php endif ?>
</div><!-- Goods info -->
<?php endif ?>


<div class="clear"></div>

<!-- Photo video -->
<?php if (count($p3d) > 0 || count($photos) > 0): ?>
<div class="fl width500">
  <h2>Фото товара:</h2>

  <div class="font11 gray pb10">Всего фотографий <?php echo count($photos) ?></div>
  <ul class="previewlist">
    <!-- IVN '.viewme' for opening in the popup; @ref='image'/'360' is a type   -->
    <?php foreach ($photos as $i => $photo): ?>
    <li class="viewstock" ref="photo<?php echo $i ?>"><b><a href="<?php echo $urls[4] . $photo->resource ?>"
                                                            class="viewme" ref="image"></a></b><img
      src="<?php echo $urls[2] . $photo->resource ?>" alt="" width="48" height="48"/></li>
    <?php endforeach ?>
    <?php if (count($p3d) > 0): ?>
    <li><a href="#" class="axonometric viewme" ref="360" title="Объемное изображение">Объемное изображение</a></li>
    <?php endif ?>
  </ul>
</div>
<?php endif ?>
<!-- /Photo video -->
<?php include_component('product', 'product_model', array('product' => $product,)) ?>
<div class="clear"></div>
<div class="mb15"></div>


<?php if (!empty($product->description)): ?>
<!-- Information -->
<h2 class="bold"><?php echo $product->name ?> - Информация о товаре</h2>
<div class="line pb15"></div>
<ul class="pb10">
  <?php echo $product->description ?>
</ul>
<!-- /Information  -->
<div class="clear"></div>
<?php endif ?>

<?php if ('kit' == $product->view): ?>
<?php //include_component('product', 'kit', array('product' => $product)) ?>
<?php else: ?>
<!-- Description -->
<h2 class="bold"><?php echo $product->name ?> - Характеристики</h2>
<div class="line pb25"></div>

<?php if (false && ($product->countParameter('show') > 5) && ($product->countParameter('list') > 0)): ?>
  <div class="descriptionlist">
    <?php include_component('product', 'property_grouped', array('product' => $product, 'view' => 'inlist')) ?>
  </div>
  <?php if (false): ?>
    <div class="pb25"><a href="#" id="toggler" class="more">Все характеристики</a></div>
    <?php endif ?>
  <div class="descriptionlist second" style="display: none;">
    <?php include_component('product', 'property_grouped', array('product' => $product)) ?>
  </div>

  <?php else: ?>
  <div class="descriptionlist">
    <?php include_component('product', 'property_grouped', array('product' => $product)) ?>
  </div>

  <?php endif ?>
<!-- /Description -->
<?php include_component('product', 'tags', array('product' => $product)) ?>
<?php endif ?>

<?php if (count($item['related'])): ?>
<?php include_partial('product/product_related', $sf_data) ?>
<?php endif ?>

<?php if (count($item['accessory'])): ?>
<?php include_partial('product/product_accessory', $sf_data) ?>
<?php endif ?>

<!-- Media -->
<div class="popup mediablock" id="bigpopup"><!-- IVN block #bigpopup is media gallery popup  -->
  <i title="Закрыть" class="close">Закрыть</i>

  <div class="float100">
    <div class="photoview">
      <div class="photobox" id="photobox"></div>
      <div class="scrollbox">
        <div><b></b></div>
      </div>
    </div>
  </div>


  <div class="leftpanel" style="margin-left:-100%">
    <div class="topblock font16">
      <div class="logobox">Enter связной</div>
      <div class="pb5"><?php echo $product->name ?></div>
      <div class="pb5">
        <?php include_partial('product/price', array('price' => $product->getFormattedPrice())) ?>
      </div>
      <div class="popup_leftpanel pb40" ref="<?php echo $product->token ?>"
           data-value='<?php echo json_encode($json) ?>'>
        <?php echo include_component('cart', 'buy_button', array('product' => $product, 'quantity' => 1, 'value' => array('купить', 'в корзине',),)) ?>
      </div>

      <h2>Фото:</h2>
      <ul class="previewlist">
        <?php foreach ($photos as $i => $photo): ?>
        <li class="viewstock" ref="photo<?php echo $i ?>"><b><a href="<?php echo $urls[4] . $photo->resource ?>"
                                                                class="viewme" ref="image" id="try-3"></a></b><img
          src="<?php echo $urls[2] . $photo->resource ?>" alt="" width="48" height="48"/></li>
        <?php endforeach ?>
        <?php if (count($p3d) > 0): ?>
        <li><a href="#" class="axonometric viewme" ref="360" title="Объемное изображение">Объемное изображение</a></li>
        <?php endif ?>
      </ul>
    </div>

  </div>


  <div class="scalepanel" style="float:left; margin-left:-80px">
    <div class="zoom"></div>

    <div class="scale">
      <b class="plus"></b>

      <div class=""><b class="zoomind"></b></div>
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


<?php /*
<div id="order1click-container" class="bMobDown mBR5 mW2 mW900" style="display: none">
  <div class="bMobDown__eWrap">
    <div class="bMobDown__eClose close"></div>
    <h2>Покупка в 1 клик!</h2>
    <div class="clear line pb20"></div>

    <form id="order1click-form" action="<?php echo url_for('order_1click', array('product' => $product['barcode'])) ?>" method="post"></form>

  </div>
</div>
 */
?>

<div id="ajaxgoods" class="popup width230" style="display: none">
  <div style="padding: 15px 15px 20px 5px">
    <div class="bNavLoader__eIco">
      <img src="/images/ajar.gif">
    </div>
    <div class="bNavLoader__eM">
      <p class="bNavLoader__eText">Подождите немного</p>

      <p class="bNavLoader__eText">Идет загрузка</p>
    </div>
  </div>
</div>