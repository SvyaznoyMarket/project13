<?php
/**
 * @var $page               \View\Product\IndexPage
 * @var $product            \Model\Product\Entity
 * @var $productVideos      \Model\Product\Video\Entity[]
 * @var $user               \Session\User
 * @var $accessories        \Model\Product\Entity[]
 * @var $accessoryCategory  array
 * @var $related            \Model\Product\Entity[]
 * @var $kit                \Model\Product\Entity[]
 * @var $additionalData     array
 * @var $showAccessoryUpper bool
 * @var $showRelatedUpper   bool
 * @var $shopStates         \Model\Product\ShopState\Entity[]
 * @var $creditData         array
 */
?>

<?

$hasFurnitureConstructor = \App::config()->product['furnitureConstructor'] && $product->getLine() && (256 == $product->getLine()->getId()); // Серия Байкал

/** @var  $productVideo \Model\Product\Video\Entity|null */
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
  foreach ($shopStates as $shopState) {
      $shop = $shopState->getShop();
      if (!$shop) continue;

      $availableShops[] = [
          'id'        => $shop->getId(),
          'name'      => $shop->getName(),
          'address'   => $shop->getAddress(),
          'regtime'   => $shop->getRegime(),
          'longitude' => $shop->getLongitude(),
          'latitude'  => $shop->getLatitude(),
          'url'       => $page->url('shop.show', ['shopToken' => $shop->getToken(), 'regionToken' => $user->getRegion()->getToken()]),
      ];
  }
  $jsonAvailableShops = json_encode($availableShops, JSON_HEX_QUOT | JSON_HEX_APOS);

  // инфо о товаре
  $productData = [
      'id'          => $product->getId(),
      'token'       => $product->getToken(),
      'article'     => $product->getArticle(),
      'name'        => $product->getName(),
      'isSupplied'  => $product->getState() ? $product->getState()->getIsSupplier() : false,
      'stockState'  =>
          $product->getIsBuyable()
          ? 'in stock'
          : (
              ($product->getState() && $product->getState()->getIsShop())
              ? 'at shop'
              : 'out of stock'
          ),
  ];
?>
<?
    $photoList = $product->getPhoto();

    /** @var string $model3dExternalUrl */
    $model3dExternalUrl = ($productVideo instanceof \Model\Product\Video\Entity) ? $productVideo->getMaybe3d() : false;
    /** @var string $model3dImg */
    $model3dImg = ($productVideo instanceof \Model\Product\Video\Entity) ? $productVideo->getImg3d() : false;
    /** @var array $photo3dList */
    $photo3dList = [];
    /** @var array $p3d_res_small */
    $p3d_res_small = [];
    /** @var array $p3d_res_big */
    $p3d_res_big = [];

    if (!$model3dExternalUrl && !$model3dImg) {
        $photo3dList = $product->getPhoto3d();
        foreach ($photo3dList as $photo3d) {
            $p3d_res_small[] = $photo3d->getUrl(0);
            $p3d_res_big[] = $photo3d->getUrl(1);
        }
    } elseif ($model3dExternalUrl) {
        $model3dName = preg_replace('/\.swf|\.swf$/iu', '', basename($model3dExternalUrl));
        if (!strlen($model3dName)) $model3dExternalUrl = false;
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
  
    $reviewsPresent = !(empty($reviewsData['review_list']) && empty($reviewsDataPro['review_list']));
?>

<style type="text/css">
    .goodsphoto_eVideoShield {
        width: 102px;
        height: 110px;
    }
</style>

<? if ($model3dExternalUrl) : 

  $arrayToMaybe3D = [
    'init' => [
      'swf'=>$model3dExternalUrl,
      'container'=>'maybe3dModel',
      'width'=>'700px',
      'height'=>'500px',
      'version'=>'10.0.0',
      'install'=>'js/vendor/expressInstall.swf',
    ],
    'params' => [
      'menu'=> "false",
      'scale'=> "noScale",
      'allowFullscreen'=> "true",
      'allowScriptAccess'=> "always",
      'wmode'=> "direct"
    ],
    'attributes' => [
      'id'=> $model3dName,
    ],
    'flashvars'=> [
      'language'=> "auto",
    ]
    
  ];
  
?>

  <div id="maybe3dModelPopup" class="popup" data-value="<?php print $page->json($arrayToMaybe3D); ?>">
    <i class="close" title="Закрыть">Закрыть</i>
    <div id="maybe3dModelPopup_inner" style="position: relative;">
      <div id="maybe3dModel">
        <a href="http://www.adobe.com/go/getflashplayer">
            <img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" />
        </a>
      </div>
    </div>
  </div>

<? endif ?>

<? if ($model3dImg) : ?>
    <div id="3dModelImg" class="popup" data-value="<?php print $page->json($model3dImg); ?>" data-host="<?= $page->json(['http://'.App::request()->getHost()]) ?>">
        <i class="close" title="Закрыть">Закрыть</i>
    </div>
<? endif ?>

<script type="text/javascript">
    <? if ($model3dExternalUrl) : ?>
    product_3d_url = <?= json_encode($model3dExternalUrl) ?>;
    <? elseif (count($photo3dList) > 0) : ?>
    product_3d_small = <?= json_encode($p3d_res_small) ?>;
    product_3d_big = <?= json_encode($p3d_res_big) ?>;
    <? endif ?>
</script>

<div id="productInfo" data-value="<?= $page->json($productData) ?>"></div>

<!-- похожие товары -->
<? if (!$product->getIsBuyable() && $product->getState()->getIsShop()  && \App::config()->smartengine['pull']): ?>
<div class="clear"></div>

<div class="lifted">
  <script type="text/html" id="similarGoodTmpl">
    <div class="bSimilarGoodsSlider_eGoods fl" <% if (data != undefined ) { %> data-article="<%=data.article%>" data-pos="<%=data.position%>" data-name="<%=data.name%>" <% } %> >
      <a class="bSimilarGoodsSlider_eGoodsImg fl" href="<%=link%>"><img width="83" height="83" src="<%=image%>"/></a>
      <div class="bSimilarGoodsSlider_eGoodsInfo fl">
        <div class="goodsbox__rating rate<%=rating%>"><div class="fill"></div></div>
        <h3><a href="<%=link%>"><%=name%></a></h3>
        <div class="font18 pb10 mSmallBtns"><span class="price"><%=price%></span> <span class="rubl">p</span></div>
      </div>
    </div>
  </script>
  <div class="bSimilarGoods mProduct clearfix">
    <div class="bSimilarGoods_eCorner"><div></div></div>
    <div class="bSimilarGoods_eLeftCaption fl">
      Товар есть только в&nbsp;магазинах. Вы&nbsp;можете заказать похожий товар.
    </div>
    <div id="similarGoodsSlider" class="bSimilarGoodsSlider fr" data-url="<?= $page->url('smartengine.pull.product_similar', ['productId' => $product->getId()]) ?>">
      <a class="bSimilarGoodsSlider_eArrow mLeft" href="#"></a>
      <a class="bSimilarGoodsSlider_eArrow mRight" href="#"></a>
      <div class="bSimilarGoodsSlider_eWrap clearfix">
      </div>
    </div>
  </div>
</div>
<? endif ?>

<? if ($hasFurnitureConstructor): ?>
    <? require __DIR__ . '/show/_furniture.php' ?>
<? else: ?>
    <? require __DIR__ . '/show/_default.php' ?>
<? endif ?>

<div class="clear"></div>

<? if ((bool)$product->getModel() && (bool)$product->getModel()->getProperty()): //модели ?>
<!-- Variation -->
<div class="fr width400">
    <h2>Этот товар с другими параметрами:</h2>
    <? foreach ($product->getModel()->getProperty() as $property): ?>
        <? if ($property->getIsImage()): ?>
        <div class="bDropWrap">
            <h5><?= $property->getName() ?>:</h5>

            <ul class="previewlist">
                <? foreach ($property->getOption() as $option): ?>
                <li>
                    <a href="<?= $option->getProduct()->getLink() ?>" <?= ($product->getId() == $option->getProduct()->getId()) ? ' class="current"' : '' ?> title="<?= $page->escape($option->getHumanizedName()) ?>">
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

<div class="clear"></div>

<? if ($showAccessoryUpper && (bool)$accessories && \App::config()->product['showAccessories']): ?>
    <div<? if ($accessoryCategory) print ' class="acess-box"' ?>>
      <?= $page->render('product/_slider', ['product' => $product, 'productList' => array_values($accessories), 'totalProducts' => count($product->getAccessoryId()), 'itemsInSlider' =>  $accessoryCategory ? \App::config()->product['itemsInAccessorySlider'] : \App::config()->product['itemsInSlider'], 'page' => 1, 'title' => 'Аксессуары', 'url' => $page->url('product.accessory', ['productToken' => $product->getToken()]), 'gaEvent' => 'Accessorize', 'showCategories' => (bool)$accessoryCategory, 'accessoryCategory' => $accessoryCategory, 'additionalData' => $additionalData]) ?>
    </div>
<? endif ?>

<? if ($showRelatedUpper && (bool)$related && \App::config()->product['showRelated']): ?>
    <?= $page->render('product/_slider', ['product' => $product, 'productList' => array_values($related), 'totalProducts' => count($product->getRelatedId()), 'itemsInSlider' => \App::config()->product['itemsInSlider'], 'page' => 1, 'title' => 'С этим товаром также покупают', 'url' => $page->url('product.related', ['productToken' => $product->getToken()]), 'additionalData' => $additionalData]) ?>
<? endif ?>

<? if (false && \App::config()->smartengine['pull']): ?>
<!--div class="clear"></div>
<div id="product_also_bought-container" data-url="<? //echo url_for('smartengine_alsoBought', ['product' => $product->getId()]) ?>" style="margin-top: 20px;"></div-->
<? endif ?>

<?php if (\App::config()->smartengine['pull']): ?>
<div class="clear"></div>
<div id="product_user-also_viewed-container" data-url="<?= $page->url('product.recommended', ['productId' => $product->getId()]) ?>" style="margin-top: 20px;"></div>
<? endif ?>

<? if (false && \App::config()->smartengine['pull']): ?>
<!--div class="clear"></div>
<div id="product_user-recommendation-container" data-url="<? //echo url_for('smartengine_userRecommendation', ['product' => $product->getId()]) ?>" style="margin-top: 20px;"><h3>Recommendations for user...</h3></div-->
<? endif ?>

<? $description = $product->getDescription(); ?>
<? if (!empty($description)): ?>
<!-- Information -->
<div class="clear"></div>
<h2 class="bold">Информация о товаре</h2>
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

  <div class="mediablock__eImgWrapper fl mW100ps">
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
        <?= $page->render('cart/_button', ['product' => $product, 'disabled' => !$product->getIsBuyable(), 'url' => $hasFurnitureConstructor ? $page->url('cart.product.setList') : null]) ?>
      </div>

      <h2>Фото:</h2>
      <ul class="previewlist">
        <? foreach ($photoList as $photo): ?>
        <li class="viewstock" ref="photo<?= $photo->getId() ?>">
        	<a href="<?= $photo->getUrl(4) ?>" class="viewme" ref="image" id="try-3">
        		<img src="<?= $photo->getUrl(2) ?>" alt="<?= $page->escape($product->getName()) ?>" title="<?= $page->escape($product->getName()) ?>" width="48" height="48" />
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

    <form id="order1click-form" action="<?= $page->url('order.1click', ['product' => $product->getBarcode()]) ?>" method="post"></form>

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
    <div id="productVideo" class="blackPopup blackPopupVideo">
      <div class="close"></div>
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


<h2 class="bold">Характеристики</h2>
<div class="line pb5"></div>
<div class="descriptionWrapper">
  <? $groupedProperties = $product->getGroupedProperties();?>
  <? $propertiesShown = 0; ?>
  <? // код в условии if(false) нужен для сворачивающихся/разворачивающихся характеристик ?>
  <? // Оля просила не удалять, а закоментировать, на случай если они решат их вернуть ?>
  <? // перед удалением уточнить у Оли не нужен ли он больше ?>
  <? if(false && $reviewsPresent) { ?>
    <div class="descriptionlist short">
        <? foreach ($groupedProperties as $groupKey => $group): ?>
        <? if (!count($group['properties'])) continue ?>
            <div class="pb15"><strong><?= $group['group']->getName() ?></strong></div>
            <? foreach ($group['properties'] as $propertyKey => $property): ?>
            <? /** @var $property \Model\Product\Property\Entity  */?>
                <div class="point">
                    <div class="title"><h3><?= $property->getName() ?></h3>
                      <? if ($property->getHint()): ?>
                      <div class="bHint fl">
                        <a class="bHint_eLink"><?= $property->getName() ?></a>
                        <div class="bHint_ePopup popup">
                          <div class="close"></div>
                          <div class="bHint-text"><?= $property->getHint() ?></div>
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
                                <div class="bHint-text"><?= $property->getValueHint() ?></div>
                            </div>
                        </div>
                        <? endif ?>
                    </div>
                </div>
                <? 
                  $propertiesShown++;
                  unset($groupedProperties[$groupKey]['properties'][$propertyKey]);
                  if(empty($groupedProperties[$groupKey]['properties'])) unset($groupedProperties[$groupKey]);
                  if($propertiesShown >= 10) break;
                ?>
            <? endforeach ?>
            <? if($propertiesShown >= 10) break; ?>
        <? endforeach ?>
    </div>
  <? } ?>
  <? // код в условии if(false) нужен для сворачивающихся/разворачивающихся характеристик ?>
  <? // Оля просила не удалять, а закоментировать, на случай если они решат их вернуть ?>
  <? // перед удалением уточнить у Оли не нужен ли он больше ?>
  <div class="descriptionlist<?= false && $reviewsPresent ? ' hf' : '' ?>">
      <? $showGroupName = true ?>
      <? foreach ($groupedProperties as $key => $group): ?>
      <? if (!count($group['properties'])) continue ?>
          <? if($showGroupName) { ?>
            <div class="pb15"><strong><?= $group['group']->getName() ?></strong></div>
          <? } ?>
          <? $showGroupName = true ?>
          <? foreach ($group['properties'] as $property): ?>
          <? /** @var $property \Model\Product\Property\Entity  */?>
              <div class="point">
                  <div class="title"><h3><?= $property->getName() ?></h3>
                    <? if ($property->getHint()): ?>
                    <div class="bHint fl">
                      <a class="bHint_eLink"><?= $property->getName() ?></a>
                      <div class="bHint_ePopup popup">
                        <div class="close"></div>
                        <div class="bHint-text"><?= $property->getHint() ?></div>
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
                              <div class="bHint-text"><?= $property->getValueHint() ?></div>
                          </div>
                      </div>
                      <? endif ?>
                  </div>
              </div>
              <? $propertiesShown++; ?>
          <? endforeach ?>
      <? endforeach ?>
  </div>
</div>
<div class="clear"></div>
<? // код в условии if(false) нужен для сворачивающихся/разворачивающихся характеристик ?>
<? // Оля просила не удалять, а закоментировать, на случай если они решат их вернуть ?>
<? // перед удалением уточнить у Оли не нужен ли он больше ?>
<? if(false && $reviewsPresent && $propertiesShown > 10) { ?>
  <div id="productDescriptionToggle" class="contourButton mb15 button width250">Показать все характеристики</div>
<? } ?>

<div class="bReviewsOld">
    <? if (\App::config()->product['reviewEnabled'] && $reviewsPresent): ?>
      <h2 id="reviewsSectionHeader" class="bold">Обзоры и отзывы</h2>
      <div id="ReviewsSummary" class="bReviewsSummary clearfix">
          <?= $page->render('product/_reviewsSummary', ['reviewsData' => $reviewsData, 'reviewsDataPro' => $reviewsDataPro, 'reviewsDataSummary' => $reviewsDataSummary]) ?>
      </div>

      <? if (!empty($reviewsData['review_list'])) { ?>
          <div id="reviewsWrapper" class="bReviewsWrapper" data-product-id="<?= $product->getId() ?>" data-page-count="<?= $reviewsData['page_count'] ?>" data-container="reviewsUser" data-reviews-type="user">
      <? } elseif(!empty($reviewsDataPro['review_list'])) { ?>
      <div id="reviewsWrapper" class="bReviewsWrapper" data-product-id="<?= $product->getId() ?>" data-page-count="<?= $reviewsDataPro['page_count'] ?>" data-container="reviewsPro" data-reviews-type="pro">
          <? } ?>
      <?= $page->render('product/_reviews', ['reviewsData' => $reviewsData, 'reviewsDataPro' => $reviewsDataPro]) ?>
      </div>
    <? endif ?>
</div> 

<? if (!$showAccessoryUpper && count($product->getAccessoryId()) && \App::config()->product['showAccessories']): ?>
    <?= $page->render('product/_slider', ['product' => $product, 'productList' => array_values($accessories), 'totalProducts' => count($product->getAccessoryId()), 'itemsInSlider' => $accessoryCategory ? \App::config()->product['itemsInAccessorySlider'] : \App::config()->product['itemsInSlider'], 'page' => 1, 'title' => 'Аксессуары', 'url' => $page->url('product.accessory', array('productToken' => $product->getToken())), 'gaEvent' => 'Accessorize', 'showCategories' => (bool)$accessoryCategory, 'accessoryCategory' => $accessoryCategory, 'additionalData' => $additionalData]) ?>
<? endif ?>

<? if (!$showRelatedUpper && count($related) && \App::config()->product['showRelated']): ?>
    <?= $page->render('product/_slider', ['product' => $product, 'productList' => array_values($related), 'totalProducts' => count($product->getRelatedId()), 'itemsInSlider' => \App::config()->product['itemsInSlider'], 'page' => 1, 'title' => 'С этим товаром также покупают', 'url' => $page->url('product.related', array('productToken' => $product->getToken())), 'additionalData' => $additionalData]) ?>
<? endif ?>

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

        <?= $page->render('cart/_button', array('product' => $product, 'disabled' => !$product->getIsBuyable(), 'url' => $hasFurnitureConstructor ? $page->url('cart.product.setList') : null, 'gaEvent' => 'Add2Basket_vnizu', 'gaTitle' => 'Добавление в корзину')) ?>
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
    <div class="pb10"><strong class="font34"><span class="bProductCardRightCol__ePrice"><?= $page->helper->formatPrice($product->getPrice()) ?></span> <span class="rubl">p</span></strong></div>
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

<?= $page->tryRender('product/_tag', ['product' => $product]) ?>

<? if (\App::config()->smartengine['push']): ?>
<div id="product_view-container" data-url="<?= $page->url('smartengine.push.product_view', array('productId' => $product->getId())) ?>"></div>
<? endif ?>

<? if ($product->getIsBuyable()): echo $page->render('order/form-oneClick'); endif; ?>


<? if (\App::config()->analytics['enabled']): ?>
    <?= $page->tryRender('product/partner-counter/_cityads', ['product' => $product]) ?>
    <?= $page->tryRender('product/partner-counter/_recreative', ['product' => $product]) ?>
<? endif ?>