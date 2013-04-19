<?php
/**
 * @var $page               \View\Product\IndexPage
 * @var $product            \Model\Product\Entity
 * @var $productVideos      \Model\Product\Video\Entity[]
 * @var $user               \Session\User
 * @var $accessories        \Model\Product\Entity[]
 * @var $related            \Model\Product\Entity[]
 * @var $kit                \Model\Product\Entity[]
 * @var $showAccessoryUpper bool
 * @var $showRelatedUpper   bool
 * @var $shopsWithQuantity  array
 */
?>

<?
$productVideo = reset($productVideos);
?>
<?
  $json = json_encode(array (
    'jsref'        => $product->getToken(),
    'jstitle'      => $product->getName(),
    'jsprice'      => $product->getPrice(),
    'jsimg'        => $product->getImageUrl(3),
    'jsbimg'       => $product->getImageUrl(2),
    'jsshortcut'   => $product->getArticle(),
    'jsitemid'     => $product->getId(),
    'jsregionid'   => $user->getRegion()->getId(),
    'jsregionName' => $user->getRegion()->getName(),
    'jsstock'      => 10,
  ),  JSON_HEX_QUOT | JSON_HEX_APOS);

  $availableShops = [];
  foreach ($shopsWithQuantity as $shopWithQuantity) {
      /** @var $shop \Model\Shop\Entity */
      $shop = $shopWithQuantity['shop'];
      $availableShops[] = array(
          'id'        => $shop->getId(),
          'name'      => $shop->getName(),
          'address'   => $shop->getAddress(),
          'regtime'   => $shop->getRegime(),
          'longitude' => $shop->getLongitude(),
          'latitude'  => $shop->getLatitude(),
          'url'       => $page->url('shop.show', array('shopToken' => $shop->getToken(), 'regionToken' => $user->getRegion()->getToken())),
      );
  }
  $jsonAvailableShops = json_encode($availableShops, JSON_HEX_QUOT | JSON_HEX_APOS);
?>
<?
  $photoList = $product->getPhoto();
  $photo3dList = $product->getPhoto3d();
  $p3d_res_small = [];
  $p3d_res_big = [];
  foreach ($photo3dList as $photo3d)
  {
    $p3d_res_small[] = $photo3d->getUrl(0);
    $p3d_res_big[] = $photo3d->getUrl(1);
  }

  $showAveragePrice = \App::config()->product['showAveragePrice'] && !$product->getPriceOld() && $product->getPriceAverage();

    $adfox_id_by_label = 'adfox400';
    if ($product->getLabel()) {
        switch ($product->getLabel()->getId()) {
            case \Model\Product\Label\Entity::LABEL_PROMO:
                $adfox_id_by_label = 'adfox400counter';
                break;
            case \Model\Product\Label\Entity::LABEL_CREDIT:
                $adfox_id_by_label = 'adfoxWowCredit';
                break;
            case \Model\Product\Label\Entity::LABEL_GIFT:
                $adfox_id_by_label = 'adfoxGift';
                break;
        }
    }
?>

<style type="text/css">
    .goodsphoto_eVideoShield {
        width: 102px;
        height: 110px;
    }
</style>

<script type="text/javascript">
  product_3d_small = <?= json_encode($p3d_res_small) ?>;
  product_3d_big = <?= json_encode($p3d_res_big) ?>;
</script>

<!-- похожие товары -->
<? if (!$product->getIsBuyable() && $product->getState()->getIsShop()  && \App::config()->smartengine['pull']): ?>
<div class="clear"></div>

<div class="lifted">
  <script type="text/html" id="similarGoodTmpl">
    <div class="bSimilarGoodsSlider_eGoods fl">
      <a class="bSimilarGoodsSlider_eGoodsImg fl" href="<%=link%>"><img width="83" height="83" src="<%=image%>"/></a>
      <div class="bSimilarGoodsSlider_eGoodsInfo fl">
        <div class="goodsbox__rating rate<%=rating%>"><div class="fill"></div></div>
        <h3><a href="<%=link%>"><%=name%></a></h3>
        <div class="font18 pb10 mSmallBtns"><span class="price"><%=price%></span> <span class="rubl">p</span></div>
      </div>
    </div>
  </script>
  <div class="bSimilarGoods clearfix">
    <div class="bSimilarGoods_eCorner"><div></div></div>
    <div class="bSimilarGoods_eLeftCaption fl">
      Товар есть только в&nbsp;магазинах. Вы&nbsp;можете заказать похожий товар.
    </div>
    <div id="similarGoodsSlider" class="bSimilarGoodsSlider fr" data-url="<?= $page->url('smartengine.pull.product_similar', array('productId' => $product->getId())) ?>">
      <a class="bSimilarGoodsSlider_eArrow mLeft" href="#"></a>
      <a class="bSimilarGoodsSlider_eArrow mRight" href="#"></a>
      <div class="bSimilarGoodsSlider_eWrap clearfix">
      </div>
    </div>
  </div>
</div>
<? endif ?>

<div class="goodsphoto">
  <? if ($productVideo): ?><a class="goodsphoto_eVideoShield" href="#"></a><? endif ?>

  <a href="<?= $product->getImageUrl(4) ?>" class="viewme" ref="image" onclick="return false">
    <? if ($product->getLabel()): ?>
    <img class="bLabels" src="<?= $product->getLabel()->getImageUrl(1) ?>" alt="<?= $product->getLabel()->getName() ?>" />
    <? endif ?>
    <img class="mainImg" src="<?= $product->getImageUrl(3) ?>" alt="<?=$product->getName()?>" title="<?=$product->getName()?>" width="500" height="500" />
  </a>
</div>
<div style="display:none;" id="stock">
  <!-- list of images 500*500 for preview -->
  <? foreach ($photoList as $photo): ?>
  <img src="<?= $photo->getUrl(3) ?>" alt="" data-url="<?= $photo->getUrl(4) ?>" ref="photo<?= $photo->getId() ?>" width="500" height="500" title="" />
  <? endforeach ?>
</div>

<!-- Goods info -->
<div class="goodsinfo bGood">
  <div class="bGood__eArticle clearfix">
    <div class="fr">
        <div id="testFreak" class="jsanalytics"><a id="tfw-badge" href="http://www.testfreaks.ru"></a></div>
    </div>
    <span>Артикул #<span  itemprop="productID"><?= $product->getArticle() ?></span></span>
  </div>

  <div class="font14 pb15" itemprop="description"><?= $product->getTagline() ?></div>
  <div class="clear"></div>

  <? if($product->getPriceOld() && !$user->getRegion()->getHasTransportCompany()): ?>
  <div style="text-decoration: line-through; font: normal 18px verdana; letter-spacing: -0.05em; color: #6a6a6a;"><span class="price"><?= $page->helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span></div>
  <? elseif($showAveragePrice): ?>
  <div class="mOurGray">
    Средняя цена в магазинах города*<br/><div class='mOurGray mIco'><span class="price"><?= $page->helper->formatPrice($product->getPriceAverage()) ?></span> <span class="rubl">p</span> &nbsp;</div>
  </div>
  <div class="clear"></div>
  <div class="clear mOur pt10 <? if ($product->hasSaleLabel()) echo 'red'; ?>">Наша цена</div>
  <? endif ?>

  <div class="fl pb15" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
    <link itemprop="availability" href="http://schema.org/OutOfStock" />  
    <div class="pb10 <? if ($product->hasSaleLabel()) echo 'red'; ?>"><strong class="font34"><span class="price" itemprop="price"><?= $page->helper->formatPrice($product->getPrice()) ?></span> <meta itemprop="priceCurrency" content="RUB"><span class="rubl">p</span></strong></div>
    <? if ($product->getIsBuyable()): ?>
    <div class="pb5"><strong class="orange">Есть в наличии</strong></div>
    <? endif ?>
  </div>


  <div class="fr ar pb15">
    
    <div class="goodsbarbig mSmallBtns" ref="<?= $product->getToken() ?>" data-value='<?= $json ?>'>
    <? if ($product->getIsBuyable()): ?>
    <div class='bCountSet'>
        <? if (!$user->getCart()->hasProduct($product->getId())): ?>
        <a class='bCountSet__eP' href="#">+</a><a class='bCountSet__eM' href="#">-</a>
        <? else: ?>
        <a class='bCountSet__eP disabled' href="#">&nbsp;</a><a class='bCountSet__eM disabled' href="#">&nbsp;</a>
        <? endif ?>
        <span><?= $user->getCart()->hasProduct($product->getId()) ? $user->getCart()->getQuantityByProduct($product->getId()) : 1 ?> шт.</span>
      </div>
    <?php endif ?>
      <?= $page->render('cart/_button', array('product' => $product, 'disabled' => !$product->getIsBuyable())) ?>
    </div>
    <? if (!$product->getIsBuyable() && $product->getState()->getIsShop()): ?>
      <div class="notBuying font12">
          <div class="corner"><div></div></div>
          Только в магазинах
      </div>
    <? endif ?>
    <? if ($product->getIsBuyable()): ?>
    <div class="pb5"><strong>
      <a href=""
         data-model='<?= $json ?>'
         link-output='<?= $page->url('order.1click', array('product' => $product->getToken())) ?>'
         link-input='<?= $page->url('product.delivery_1click') ?>'
         class="red underline order1click-link-new">Купить быстро в 1 клик</a>
    </strong></div>
    <? endif ?>
  </div>
  <? if (!$product->getIsBuyable() && $product->getState()->getIsShop()): ?>
  <div class="fr ar pb15">

      <div class="vitrin" id="availableShops" data-shops='<?= $jsonAvailableShops ?>'>
        <div class="line pb15"></div>
        <p class="font18 orange">Этот товар вы можете купить только в магазин<?= (count($shopsWithQuantity) == 1) ? 'е' : 'ах' ?></p>
        <ul id="listAvalShop">
          <? $i = 0; foreach ($shopsWithQuantity as $shopWithQuantity): $i++?>
              <li<?= $i > 3 ? ' class="hidden"' : ''?>>
                <a class="fr dashedLink shopLookAtMap" href="#">Посмотреть на карте</a>
                <?= '<a class="avalShopAddr" href="'.$page->url('shop.show', array('shopToken' => $shopWithQuantity['shop']->getToken(), 'regionToken' => $user->getRegion()->getToken())).'" class="underline">'.$shopWithQuantity['shop']->getName().'</a>' ?>
                <strong class="font12 orange db pt10"><?= ($shopWithQuantity['quantity'] > 5 ? 'есть в наличии' : ($shopWithQuantity['quantity'] > 0 ? 'осталось мало' : ($shopWithQuantity['quantityShowroom'] > 0 ? 'есть только на витрине' : ''))) ?></strong>
              </li>
          <? endforeach ?>
        </ul>
        <?php if (count($shopsWithQuantity) > 3): ?>
          <a id="slideAvalShop" class="orange strong dashedLink font18" href="#">Еще <?= count($shopsWithQuantity) - 3 ?> <?= $page->helper->numberChoice(count($shopsWithQuantity) - 3, ['магазин', 'магазина', 'магазинов']) ?></a>
        <?php endif ?>
      </div>
  </div>
  <? endif ?>
  <div class="line pb15"></div>


  <? if ($product->getIsBuyable()): ?>

  <? if ($dataForCredit['creditIsAllowed'] && !$user->getRegion()->getHasTransportCompany()) : ?>
  <div class="creditbox">
    <div class="creditboxinner">
      <div class="creditLeft">от <span class="font24"><b class="price"></b> <b class="rubl">p</b></span> в кредит</div>
      <div class="fr pt5"><label class="bigcheck " for="creditinput"><b></b>Беру в кредит
        <input id="creditinput" type="checkbox" name="creditinput" autocomplete="off"/></label>
      </div>
      <div class="clear"></div>
    </div>
  </div>
  <? endif; ?>

  <? if ($dataForCredit['creditIsAllowed']) : ?>
    <input data-model="<?= $page->escape($dataForCredit['creditData']) ?>" id="dc_buy_on_credit_<?= $product->getArticle(); ?>" name="dc_buy_on_credit" type="hidden" />
  <? endif; ?>

  <? elseif ($user->getRegion()->getHasTransportCompany()): ?>
    <? if (\App::config()->product['globalListEnabled'] && (bool)$product->getNearestCity()): ?>
        <?= $page->render('product/_nearestCity', array('product' => $product)) ?>
    <? else: ?>
        <p>Этот товар мы доставляем только в регионах нашего присутствия</p>
    <? endif ?>
  <?php endif ?>

  <? if ($product->getIsBuyable()): ?>
    <div class="bDeliver2 delivery-info" id="product-id-<?= $product->getId() ?>" data-shoplink="<?= $page->url('product.stock', array('productPath' => $product->getPath())) ?>" data-calclink="<?= $page->url('product.delivery') ?>">
      <h4>Как получить заказ?</h4>
      <ul>
        <li>
          <h5>Идет расчет условий доставки...</h5>
        </li>
      </ul>
    </div>
  <?php endif ?>

  <? if (\App::config()->adFox['enabled']): ?>
  <div style="margin-bottom: 20px;">
    <div class="adfoxWrapper" id="<?= $adfox_id_by_label ?>"></div>
  </div>
  <? endif ?>

  <? if ($product->getIsBuyable()): ?>
    <?= $page->render('service/_listByProduct', array('product' => $product)) ?>
    <?= $page->render('warranty/_listByProduct', array('product' => $product)) ?>
  <? endif ?>

</div>
<!-- /Goods info -->

<div class="clear"></div>

<!-- Photo video -->
<? if (count($photo3dList) > 0 || count($photoList) > 0): ?>
<div class="fl width500">
  <h2>Фото товара:</h2>
  <div class="font11 gray pb10">Всего фотографий <?= count($photoList) ?></div>
  <ul class="previewlist">
    <? foreach ($photoList as $photo): ?>
    <li class="viewstock" ref="photo<?= $photo->getId() ?>">
    	<a href="<?= $photo->getUrl(4) ?>" class="viewme" ref="image">
    		<img src="<?= $photo->getUrl(2) ?>" alt="<?=$product->getName()?>" title="<?=$product->getName()?>" width="48" height="48" />
    	</a>
    </li>
    <? endforeach ?>
    <? if (count($photo3dList) > 0): ?>
    <li><a href="#" class="axonometric viewme" ref="360" title="Объемное изображение">Объемное изображение</a></li>
    <? endif ?>
  </ul>
</div>
<? endif ?>
<!-- /Photo video -->

<? if((bool)$product->getModel() && (bool)$product->getModel()->getProperty()): //модели ?>
<!-- Variation -->
<div class="fr width400">
    <h2>Этот товар с другими параметрами:</h2>
    <? foreach ($product->getModel()->getProperty() as $property): ?>
        <? if($property->getIsImage()): ?>
        <div class="bDropWrap">
            <h5><?= $property->getName() ?>:</h5>

            <ul class="previewlist">
                <? foreach ($property->getOption() as $option): ?>
                <li>
                    <a href="<?= $option->getProduct()->getLink() ?>" <?= ($product->getId() == $option->getProduct()->getId()) ? ' class="current"' : '' ?> title="<?= $option->getHumanizedName() ?>">
                    	<img src="<?= $option->getProduct()->getImageUrl(1) ?>" alt="<?= $option->getHumanizedName() ?>" width="48" height="48"/>
                    </a>
                </li>
                <? endforeach ?>
            </ul>
        </div>

        <div class="clear"></div>
        <? else: ?>
            <?
                $productAttribute = $product->getPropertyById($property->getId());
                if (!$productAttribute) break;
            ?>
            <div class="bDropWrap">
                <h5><?= $property->getName() ?>:</h5>

                <div class="bDropMenu">
                    <span class="bold"><a href="<?= $product->getLink() ?>"><?= $productAttribute->getStringValue() ?></a></span>
                    <div>
                        <span class="bold"><a href="<?= $product->getLink() ?>"><?= $productAttribute->getStringValue() ?></a></span>

                <? foreach ($property->getOption() as $option):?>
                    <? if ($option->getValue() == $productAttribute->getValue())continue; ?>
                    <span>
                        <a href="<?= $option->getProduct()->getLink() ?>">
                            <?= $option->getHumanizedName() ?>
                        </a>
                    </span>
                <? endforeach ?>
                    </div>

                </div>
            </div>
        <? endif ?>
    <? endforeach; ?>
</div>
<!-- /Variation -->
<? endif ?>


<? if ($showAccessoryUpper && (bool)$accessories && \App::config()->product['showAccessories']): ?>
    <?= $page->render('product/_slider', array('product' => $product, 'productList' => array_values($accessories), 'totalProducts' => count($product->getAccessoryId()), 'itemsInSlider' => \App::config()->product['itemsInSlider'], 'page' => 1, 'title' => 'Аксессуары', 'url' => $page->url('product.accessory', array('productToken' => $product->getToken())), 'gaEvent' => 'Accessorize')) ?>
<? endif ?>

<? if ($showRelatedUpper && (bool)$related && \App::config()->product['showRelated']): ?>
    <?= $page->render('product/_slider', array('product' => $product, 'productList' => array_values($related), 'totalProducts' => count($product->getRelatedId()), 'itemsInSlider' => \App::config()->product['itemsInSlider'], 'page' => 1, 'title' => 'С этим товаром также покупают', 'url' => $page->url('product.related', array('productToken' => $product->getToken())))) ?>
<? endif ?>

<? if (false && \App::config()->smartengine['pull']): ?>
<!--div class="clear"></div>
<div id="product_also_bought-container" data-url="<? //echo url_for('smartengine_alsoBought', array('product' => $product->getId())) ?>" style="margin-top: 20px;"></div-->
<? endif ?>

<?php if (\App::config()->smartengine['pull']): ?>
<div class="clear"></div>
<div id="product_user-also_viewed-container" data-url="<?= $page->url('product.recommended', ['productId' => $product->getId()]) ?>" style="margin-top: 20px;"></div>
<? endif ?>

<? if (false && \App::config()->smartengine['pull']): ?>
<!--div class="clear"></div>
<div id="product_user-recommendation-container" data-url="<? //echo url_for('smartengine_userRecommendation', array('product' => $product->getId())) ?>" style="margin-top: 20px;"><h3>Recommendations for user...</h3></div-->
<? endif ?>

<? $description = $product->getDescription(); ?>
<? if (!empty($description)): ?>
<!-- Information -->
<div class="clear"></div>
<h2 class="bold"><?= $product->getName() ?> - Информация о товаре</h2>
<div class="line pb15"></div>
<ul class="pb10">
  <?= $description ?>
</ul>
<!-- /Information  -->

<? endif ?>
<div class="clear"></div>

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
      <div class="pb5"><?= $product->getName() ?></div>
      <div class="pb5">
          <strong class="font34"><span class="price"><?= $page->helper->formatPrice($product->getPrice()) ?></span> <span class="rubl">p</span></strong>
      </div>
      <div class="goodsbarbig mSmallBtns pb40" ref="<?= $product->getToken() ?>" data-value='<?= $json ?>'>
        <?= $page->render('cart/_button', array('product' => $product, 'disabled' => !$product->getIsBuyable())) ?>
      </div>

      <h2>Фото:</h2>
      <ul class="previewlist">
        <? foreach ($photoList as $photo): ?>
        <li class="viewstock" ref="photo<?= $photo->getId() ?>">
        	<a href="<?= $photo->getUrl(4) ?>" class="viewme" ref="image" id="try-3">
        		<img src="<?= $photo->getUrl(2) ?>" alt="<?=$product->getName()?>" title="<?=$product->getName()?>" width="48" height="48" />
        	</a>
        </li>
        <? endforeach ?>
        <? if (count($photo3dList) > 0): ?>
        <li><a href="#" class="axonometric viewme" ref="360" title="Объемное изображение">Объемное изображение</a></li>
        <? endif ?>
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
<?php if ($product->getIsBuyable()): ?>
<div id="order1click-container" class="bMobDown mBR5 mW2 mW900" style="display: none">
  <div class="bMobDown__eWrap">
    <div class="bMobDown__eClose close"></div>
    <h2>Покупка в 1 клик!</h2>
    <div class="clear line pb20"></div>

    <form id="order1click-form" action="<?= $page->url('order.1click', array('product' => $product->getBarcode())) ?>" method="post"></form>

  </div>
</div>
<?php elseif ($product->getState()->getIsShop()): ?>
<!-- shopPopup -->
<script type="text/html" id="mapInfoBlock">
  <div class="bMapShops__ePopupRel">
    <h3><%=name%></h3>
    <span>Работает </span>
    <span><%=regime%></span>
    <br/>
    <span class="shopnum" style="display: none;"><%=id%></span>
  </div>
</script>
<div id="orderMapPopup" class='popup'>
  <i class='close'></i>
  <div class='bMapShops__eMapWrap' id="mapPopup" style="float: right;">
  </div>
  <div class='bMapShops__eList'>
    <script type="text/html" id="itemAvalShop_tmplPopup">
      <li ref="<%=id%>">
        <div class="bMapShops__eListNum"><img src="/images/shop.png" alt=""/></div>
        <div><%=name%></div>
        <span>Работаем</span> <span><%=regtime%></span>
      </li>
    </script>
    <h3>Выберите магазин Enter для самовывоза</h3>
    <ul id="mapPopup_shopInfo">

    </ul>
  </div>
</div>
<!-- /shopPopup -->
<?php endif; ?>

<!-- product video pop-up -->
<? if ($productVideo): ?>
    <div id="productVideo" class="blackPopup">
      <div class="close">X</div>
      <!-- <iframe width="640" height="360" src="http://rutube.ru/video/embed/6125142" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen scrolling="no"></iframe>  -->
      <!--<iframe src="http://player.vimeo.com/video/58429056?badge=0" width="500" height="250" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>-->
      <div class="productVideo_iframe"><?= $productVideo->getContent() ?></div>
    </div>
<? endif ?>
<!-- /product video pop-up -->

<div id="ajaxgoods" class="popup width230" style="display: none">
  <div  style="padding: 15px 15px 20px 5px">
    <div class="bNavLoader__eIco">
      <img src="/images/ajar.gif" />
    </div>
    <div class="bNavLoader__eM">
      <p class="bNavLoader__eText">Подождите немного</p>
      <p class="bNavLoader__eText">Идет загрузка</p>
    </div>
  </div>
</div>

<? if (2 == $product->getViewId() && count($product->getKit())): ?>
    <h2 class="bold fl"><?= $product->getName() ?> включает в себя:</h2>

    <div class="line"></div>
    <div style="width: 940px; float: none; margin: 0;" class="goodslist">
        <? $i = 0; foreach ($product->getKit() as $part): $i++ ?>
        <?= $page->render('product/show/_compact', array('product' => $kit[$part->getId()], 'kit' => $part)) ?>
        <? if (0 == ($i % 4)): ?><br class="clear" /><? endif ?>
        <? endforeach ?>

    </div>

<div class="clear pb25"></div>
<? endif ?>

<h2 class="bold"><?= $product->getName() ?> - Характеристики</h2>
<div class="line pb25"></div>
<div class="descriptionlist">

    <? foreach ($product->getGroupedProperties() as $group): ?>
    <? if (!count($group['properties'])) continue ?>
        <div class="pb15"><strong><?= $group['group']->getName() ?></strong></div>
        <? foreach ($group['properties'] as $property): ?>
        <? /** @var $property \Model\Product\Property\Entity  */?>
            <div class="point">
                <div class="title"><h3><?= $property->getName() ?></h3>
                  <? if ($property->getHint()): ?>
                  <div class="bHint fl">
                    <a class="bHint_eLink"><?= $property->getName() ?></a>
                    <div class="bHint_ePopup popup">
                      <div class="close"></div>
                      <?= $property->getHint() ?>
                    </div>
                  </div>
                  <? endif ?>
                </div>
                <div class="description fl">
                    <span class="fl mr10"><?= $property->getStringValue() ?></span>
                    <? if ($property->getValueHint()): ?>
                    <div class="bHint fl">
                        <a class="bHint_eLink"><?= $property->getStringValue() ?></a>
                        <div class="bHint_ePopup popup">
                            <div class="close"></div>
                            <?= $property->getValueHint() ?>
                        </div>
                    </div>
                    <? endif ?>
                </div>
            </div>
        <? endforeach ?>
    <? endforeach ?>

</div>

<?= $page->tryRender('product/_tag', ['product' => $product]) ?>

<? if (!$showAccessoryUpper && count($product->getAccessoryId()) && \App::config()->product['showAccessories']): ?>
    <?= $page->render('product/_slider', array('product' => $product, 'productList' => array_values($accessories), 'totalProducts' => count($product->getAccessoryId()), 'itemsInSlider' => \App::config()->product['itemsInSlider'], 'page' => 1, 'title' => 'Аксессуары', 'url' => $page->url('product.accessory', array('productToken' => $product->getToken())), 'gaEvent' => 'Accessorize')) ?>
<? endif ?>

<? if (!$showRelatedUpper && count($product->getRelatedId()) && \App::config()->product['showRelated']): ?>
    <?= $page->render('product/_slider', array('product' => $product, 'productList' => array_values($related), 'totalProducts' => count($product->getRelatedId()), 'itemsInSlider' => \App::config()->product['itemsInSlider'], 'page' => 1, 'title' => 'С этим товаром также покупают', 'url' => $page->url('product.related', array('productToken' => $product->getToken())))) ?>
<? endif ?>

<div class="line"></div>
<div class="fr ar">
    <? //if ($product->getIsBuyable() || !$product->getState()->getIsShop()): ?>
    <div class="goodsbarbig mSmallBtns" ref="<?= $product->getToken() ?>" data-value='<?= $json ?>'>

    <?php if ($product->getIsBuyable()): ?>
        <div class='bCountSet'>
            <? if (!$user->getCart()->hasProduct($product->getId())): ?>
            <a class='bCountSet__eP' href="#">+</a><a class='bCountSet__eM' href="#">-</a>
            <? else: ?>
            <a class='bCountSet__eP disabled' href="#">&nbsp;</a><a class='bCountSet__eM disabled' href="#">&nbsp;</a>
            <? endif ?>
            <span><?= $user->getCart()->getQuantityByProduct($product->getId()) ? $user->getCart()->getQuantityByProduct($product->getId()) : 1 ?> шт.</span>
        </div>
    <?php endif ?>

        <?= $page->render('cart/_button', array('product' => $product, 'disabled' => !$product->getIsBuyable(), 'gaEvent' => 'Add2Basket_vnizu', 'gaTitle' => 'Добавление в корзину')) ?>
        <? if (!$product->getIsBuyable() && $product->getState()->getIsShop()): ?>
        <div class="notBuying font12">
            <div class="corner"><div></div></div>
            Только в магазинах
        </div>
        <? endif ?>
    </div>
    <? //endif ?>
</div>
<div class="fr mBuyButtonBottom">
    <div class="pb10"><strong class="font34"><span class="price"><?= $page->helper->formatPrice($product->getPrice()) ?></span> <span class="rubl">p</span></strong></div>
</div>
<div class="fl mBuyButtonBottom onleft" >
    <h2 class="bold"><?= $product->getName() ?></h2>
</div>


<br class="clear" />

<? if ($showAveragePrice): ?>
    <div class="gray pt20 mb10">*по данным мониторинга компании Enter</div>
    <div class="clear"></div>
<? endif ?>

<?= $page->render('_breadcrumbs', array('breadcrumbs' => $breadcrumbs, 'class' => 'breadcrumbs-footer')) ?>

<? if (\App::config()->smartengine['push']): ?>
<div id="product_view-container" data-url="<?= $page->url('smartengine.push.product_view', array('productId' => $product->getId())) ?>"></div>
<? endif ?>

<? if ($product->getIsBuyable()): echo $page->render('order/form-oneClick'); endif; ?>


<? if (\App::config()->analytics['enabled']): ?>
    <?= $page->tryRender('product/partner-counter/_cityads', ['product' => $product]) ?>
    <?= $page->tryRender('product/partner-counter/_recreative', ['product' => $product]) ?>
<? endif ?>