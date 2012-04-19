<?php
#JSON data
//print_r($json);
//$json->
//	$json = array (
//		'jsref' => $product->token,
//		'jstitle' => htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'),
//		'jsprice' => $product->price,
//		'jsimg' => $product->getMainPhotoUrl(3)
//	);
//print_r($json);
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
  $p3d_res_small[] = $p3d_obj['path']['small'];
  $p3d_res_big[] = $p3d_obj['path']['big'];
}
?>
<script type="text/javascript">
  product_3d_small = <?php echo json_encode($p3d_res_small) ?>;
  product_3d_big = <?php echo json_encode($p3d_res_big) ?>;
</script>

<?php slot('after_body_block') ?>
<?php include_partial('productSoa/oneclickTemplate', array()) ?>
<?php end_slot() ?>

<div class="goodsphoto">
    <a href="<?php echo $product->getMainPhotoUrl(4)  ?>" class="viewme" ref="image" onclick="return false">
        <?php
        if ($product->label): ?>
            <?php foreach ($product->label as $label):?>
                <img class="bLabels" src="<?php echo $product->getLabelUrl($label['media_image'], 1) ?>" alt="<?php echo $label['name'] ?>" />
            <?php endforeach ?>
        <?php endif ?>
        <img src="<?php echo $product->getMainPhotoUrl(3) ?>" alt="" width="500" height="500" title="" />
    </a>
</div>
<div style="display:none;" id="stock">
  <!-- list of images 500*500 for preview -->
  <?php foreach ($photos as $i => $photo): ?>
    <img src="<?php echo $photo['path'][3] ?>" alt="" data-url="<?php echo $photo['path'][4] ?>" ref="photo<?php echo $i ?>" width="500" height="500" title="" />
<?php endforeach ?>
</div>

<!-- Goods info -->
    <div class="goodsinfo bGood">
        <div class="bGood__eArticle">
            <div class="fr">
          <span id="rating" data-url="<?php echo url_for('userProductRating_createtotal', array('rating' => 'score', 'product' => $product->id )) ?>"<?php //if ($item['rated']) echo ' data-readonly="true"' ?>>
            <?php
              echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($product->rating));
              echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($product->rating));
            ?>
          </span>
          <strong class="ml5 hf"><?php echo round($product->rating, 1) ?></strong>


				<a href="<?php echo url_for('productComment', array('product' => $product->path)) ?>" class="underline ml5">Читать отзывы</a> <span>(<?php echo $product->getCommentCount() ?>)</span>
			</div>
            <span>Артикул #<?php echo $product->article ?></span>
        </div>

        <div class="font14 pb15"><?php echo $product->preview ?></div>
        <div class="clear"></div>

        <?php if ($product->haveToShowAveragePrice()): ?>
            <div class="mOurGray">
                Средняя цена в магазинах города*<br><div class='mOurGray mIco'><?php include_partial('product/price', array('price' => $product->price_average, 'noStrong' => true, )) ?> &nbsp;</div>
            </div>
            <?php slot('additional_data') ?>
                <div class="gray pt20 mb10">*по данным мониторинга компании Enter</div>
                <div class="clear"></div>
            <?php end_slot() ?>
            <div class="clear"></div>
            <div class="clear mOur pt10 <?php if ($product->sale_label) echo 'red'; ?>">Наша цена</div>
        <?php endif ?>

        <div class="fl pb15">
            <div class="pb10 <?php if ($product->sale_label) echo 'red'; ?>"><?php include_partial('productSoa/price', array('price' => $product->getFormattedPrice())) ?></div>
            <?php if ($product->is_instock): ?>
            <div class="pb5"><strong class="orange">Есть в наличии</strong></div>
            <?php endif ?>
        </div>


        <div class="fr ar pb15">
            <div class="goodsbarbig mSmallBtns" ref="<?php echo $product->token ?>" data-value='<?php echo $json ?>'>

              <div class='bCountSet'>
                <?php if (!$product->cart_quantity): ?>
                  <a class='bCountSet__eP' href>+</a><a class='bCountSet__eM' href>-</a>
                <?php else: ?>
                  <a class='bCountSet__eP disabled' href>&nbsp;</a><a class='bCountSet__eM disabled' href>&nbsp;</a>
              	<?php endif ?>
                <span><?php echo $product->cart_quantity ? $product->cart_quantity : 1 ?> шт.</span>
              </div>

              <?php echo include_component('cart', 'buy_button', array('product' => $product, 'quantity' => 1, 'soa' => 1)) ?>
            </div>

            <?php if ( $product->is_insale && $sf_user->getRegion('region')->is_default): ?>
            <div class="pb5"><strong>
            	<a href=""
            		data-model='<?php echo $json ?>'
            		class="red underline order1click-link">Купить быстро в 1 клик</a>
            </strong></div>

            <?php endif ?>
        </div>


        <div class="line pb15"></div>


        <?php if ($product->is_insale): ?>
        <?php //include_component('productSoa', 'delivery', array('product' => $product)) ?>
        <div class="bDeliver2 delivery-info" id="product-id-<?php echo $product->id ?>" data-shoplink="<?php echo url_for('productStock', array('product' => $product->path)) ?>" data-calclink="<?php echo url_for('product_delivery', array('product' => $product->id)) ?>">
            <h4>Как получить заказ?</h4>
            <ul>
                <li>
                    <h5>Идет расчет условий доставки...</h5>
                </li>
            </ul>
        </div>
        <div class="line pb15"></div>
        <?php endif ?>

	    <div style="margin-bottom: 20px;">
		    <!--AdFox START-->
		    <!--enter-->
		    <!--Площадка: Enter.ru / * / *-->
		    <!--Тип баннера: 400x-->
		    <!--Расположение: <верх страницы>-->
		    <!-- ________________________AdFox Asynchronous code START__________________________ -->
		    <script type="text/javascript">
			    <!--
			    if (typeof(pr) == 'undefined') { var pr = Math.floor(Math.random() * 1000000); }
			    if (typeof(document.referrer) != 'undefined') {
				    if (typeof(afReferrer) == 'undefined') {
					    afReferrer = escape(document.referrer);
				    }
			    } else {
				    afReferrer = '';
			    }
			    var addate = new Date();
			    var dl = escape(document.location);
			    var pr1 = Math.floor(Math.random() * 1000000);

			    document.write('<div id="AdFox_banner_'+pr1+'"><\/div>');
			    document.write('<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>');

			    AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&amp;ps=vto&amp;p2=engb&amp;pct=a&amp;plp=a&amp;pli=a&amp;pop=a&amp;pr=' + pr +'&amp;pt=b&amp;pd=' + addate.getDate() + '&amp;pw=' + addate.getDay() + '&amp;pv=' + addate.getHours() + '&amp;prr=' + afReferrer + '&amp;dl='+dl+'&amp;pr1='+pr1);
			    // -->
		    </script>
		    <!-- _________________________AdFox Asynchronous code END___________________________ -->
	    </div>

        <?php include_component('serviceSoa', 'listByProduct', array('product' => $product)) ?>

    </div>
    <!-- /Goods info -->




<div class="clear"></div>

<!-- Photo video -->
<?php if (count($p3d) > 0 || count($photos) > 0): ?>
  <div class="fl width500">
    <h2>Фото товара:</h2>
    <div class="font11 gray pb10">Всего фотографий <?php echo count($photos) ?></div>
    <ul class="previewlist">
      <!-- IVN '.viewme' for opening in the popup; @ref='image'/'360' is a type   -->
      <?php foreach ($photos as $i => $photo): ?>
        <li class="viewstock" ref="photo<?php echo $i ?>"><b><a href="<?php echo $photo['path'][4] ?>" class="viewme" ref="image"></a></b><img src="<?php echo $photo['path'][2] ?>" alt="" width="48" height="48" /></li>
      <?php endforeach ?>
      <?php if (count($p3d) > 0): ?>
        <li><a href="#" class="axonometric viewme" ref="360" title="Объемное изображение">Объемное изображение</a></li>
  <?php endif ?>
    </ul>
  </div>
<?php endif ?>
<!-- /Photo video -->

<?php include_component('productSoa', 'product_model', array('product' => $product,)) ?>

<div class="clear"></div>

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
    <?php //include_component('productSoa', 'kit', array('product' => $product)) ?>
<?php else: ?>
<!-- Description -->
<h2 class="bold"><?php echo $product->name ?> - Характеристики</h2>
<div class="line pb25"></div>


<div class="descriptionlist">
<?php include_component('productSoa', 'property_grouped', array('product' => $product)) ?>
</div>

<!-- /Description -->
    <?php include_component('productSoa', 'tags', array('product' => $product)) ?>
<?php endif ?>

<?php //if (count($product->related)): ?>
<?php //include_partial('productSoa/product_related', $sf_data) ?>


<?php //if (count($product->accessories)): ?>
<?php //include_partial('productSoa/product_accessory', $sf_data) ?>
<?php //endif ?>

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
      <div class="pb5"><?php echo $product->name ?></div>
      <div class="pb5">
      <?php include_partial('productSoa/price', array('price' => $product->getFormattedPrice())) ?>
      </div>
      <div class="popup_leftpanel pb40" ref="<?php echo $product->token ?>" data-value='<?php echo $json ?>'>
      	<?php echo include_component('cart', 'buy_button', array('product' => $product, 'quantity' => 1, 'soa' => 1, 'value' => array('купить', 'в корзине',), )) ?>
      </div>

      <h2>Фото:</h2>
      <ul class="previewlist">
        <?php foreach ($photos as $i => $photo): ?>
          <li class="viewstock" ref="photo<?php echo $i ?>"><b><a href="<?php echo $photo['path'][4] ?>" class="viewme" ref="image" id="try-3"></a></b><img src="<?php echo $photo['path'][2] ?>" alt="" width="48" height="48" /></li>
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

<div id="ajaxgoods" class="popup width230" style="display: none">
  <div  style="padding: 15px 15px 20px 5px">
    <div class="bNavLoader__eIco">
      <img src="/images/ajar.gif">
    </div>
    <div class="bNavLoader__eM">
      <p class="bNavLoader__eText">Подождите немного</p>
      <p class="bNavLoader__eText">Идет загрузка</p>
    </div>
  </div>
</div>
