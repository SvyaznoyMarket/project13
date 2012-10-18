<?php
/**
 * @var $page               \View\Product\IndexPage
 * @var $product            \Model\Product\Entity
 * @var $user               \Session\User
 * @var $accessories        \Model\Product\Entity[]
 * @var $related            \Model\Product\Entity[]
 * @var $kit                \Model\Product\Entity[]
 * @var $showAccessoryUpper bool
 * @var $showRelatedUpper   bool
 */
?>

<?php
  $json = json_encode(array (
    'jsref'        => $product->getToken(),
    'jstitle'      => $page->escape($product->getName()),
    'jsprice'      => $product->getPrice(),
    'jsimg'        => $product->getImageUrl(3),
    'jsbimg'       =>  $product->getImageUrl(2),
    'jsshortcut'   =>  $product->getArticle(),
    'jsitemid'     =>  $product->getId(),
    'jsregionid'   => $user->getRegion()->getId(),
    'jsregionName' => $user->getRegion()->getName(),
    'jsstock'      => 10,
  ));
?>
<?php
  $photoList = $product->getPhoto();
  $photo3dList = $product->getPhoto3d();
  $p3d_res_small = array();
  $p3d_res_big = array();
  foreach ($photo3dList as $photo3d)
  {
    $p3d_res_small[] = $photo3d->getUrl(0);
    $p3d_res_big[] = $photo3d->getUrl(1);
  }

  $showAveragePrice = !$product->getPriceOld() && $product->getPriceAverage();

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
<script type="text/javascript">
  product_3d_small = <?php echo json_encode($p3d_res_small) ?>;
  product_3d_big = <?php echo json_encode($p3d_res_big) ?>;
</script>

<div class="goodsphoto">
  <a href="<?php echo $product->getImageUrl(4) ?>" class="viewme" ref="image" onclick="return false">
    <?php if ($product->getLabel()): ?>
    <img class="bLabels" src="<?php echo $product->getLabel()->getImageUrl(1) ?>" alt="<?php echo $product->getLabel()->getName() ?>" />
    <?php endif ?>
    <img class="mainImg" src="<?php echo $product->getImageUrl(3) ?>" alt="" width="500" height="500" title="" />
  </a>
</div>
<div style="display:none;" id="stock">
  <!-- list of images 500*500 for preview -->
  <?php foreach ($photoList as $photo): ?>
  <img src="<?php echo $photo->getUrl(3) ?>" alt="" data-url="<?php echo $photo->getUrl(4) ?>" ref="photo<?php echo $photo->getId() ?>" width="500" height="500" title="" />
  <?php endforeach ?>
</div>

<!-- Goods info -->
<div class="goodsinfo bGood">
  <div class="bGood__eArticle">
    <div class="fr">
      <span id="rating" data-url="<?php echo $page->url('product.rating.create_total', array('rating' => 'score', 'productId' => $product->getId() )) ?>">
        <?php
        echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($product->getRating()));
        echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($product->getRating()));
        ?>
      </span>
      <strong class="ml5 hf"><?php echo round($product->getRating(), 1) ?></strong>
      <a href="<?php echo $product->getLink().'/comments' ?>" class="underline ml5">Читать отзывы</a> <span>(<?php echo $product->getCommentCount() ?>)</span>
    </div>
    <span>Артикул #<?php echo $product->getArticle() ?></span>
  </div>

  <div class="font14 pb15"><?php echo $product->getTagline() ?></div>
  <div class="clear"></div>

  <?php if($product->getPriceOld()): ?>
  <div style="text-decoration: line-through; font: normal 18px verdana; letter-spacing: -0.05em; color: #6a6a6a;"><span class="price"><?php echo $page->helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span></div>
  <?php elseif($showAveragePrice): ?>
  <div class="mOurGray">
    Средняя цена в магазинах города*<br><div class='mOurGray mIco'><span class="price"><?php echo $page->helper->formatPrice($product->getPriceAverage()) ?></span> <span class="rubl">p</span> &nbsp;</div>
  </div>
  <div class="clear"></div>
  <div class="clear mOur pt10 <?php if ($product->hasSaleLabel()) echo 'red'; ?>">Наша цена</div>
  <?php endif ?>

  <div class="fl pb15">
    <div class="pb10 <?php if ($product->hasSaleLabel()) echo 'red'; ?>"><strong class="font34"><span class="price"><?php echo $page->helper->formatPrice($product->getPrice()) ?></span> <span class="rubl">p</span></strong></div>
    <?php if ($product->getIsBuyable()): ?>
    <div class="pb5"><strong class="orange">Есть в наличии</strong></div>
    <?php endif ?>
  </div>


  <div class="fr ar pb15">
    <?php if ( $product->getState()->getIsBuyable()): ?>
    <div class="goodsbarbig mSmallBtns" ref="<?php echo $product->getToken() ?>" data-value='<?php echo $json ?>'>

      <div class='bCountSet'>
        <?php if (!$user->getCart()->hasProduct($product->getId())): ?>
        <a class='bCountSet__eP' href>+</a><a class='bCountSet__eM' href>-</a>
        <?php else: ?>
        <a class='bCountSet__eP disabled' href>&nbsp;</a><a class='bCountSet__eM disabled' href>&nbsp;</a>
        <?php endif ?>
        <span><?php echo $user->getCart()->hasProduct($product->getId()) ? $user->getCart()->getQuantityByProduct($product->getId()) : 1 ?> шт.</span>
      </div>
      <?php echo $page->render('cart/_button', array('product' => $product, 'disabled' => !$product->getIsBuyable())) ?>
    </div>
    <div class="pb5"><strong>
      <a href=""
         data-model='<?php echo $json ?>'
         link-output='<?php echo $page->url('order.1click', array('product' => $product->getBarcode())) ?>'
         link-input='<?php echo $page->url('product.delivery_1click') ?>'
         class="red underline order1click-link-new">Купить быстро в 1 клик</a>
    </strong></div>
    <?php else: ?>
    <span class="font16 orange">Для покупки товара<br />обратитесь в Контакт-сENTER</span>
    <?php endif ?>
  </div>


  <div class="line pb15"></div>
  <?php if ($dataForCredit['creditIsAllowed']) : ?>
  <div class="creditbox">
    <div class="creditboxinner">
      от <span class="font24"><span class="price"></span> <span class="rubl">p</span></span> в кредит
      <div class="fr pt5"><label class="bigcheck " for="creditinput"><b></b>Беру в кредит
        <input id="creditinput" type="checkbox" name="creditinput" autocomplete="off"/></label></div>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($dataForCredit['creditIsAllowed']) : ?>
  <input data-model="<?php echo $page->escape($dataForCredit['creditData']) ?>" id="dc_buy_on_credit_<?php echo $product->getArticle(); ?>" name="dc_buy_on_credit" type="hidden" />
  <?php endif; ?>

  <?php if ($product->getIsBuyable()): ?>
  <div class="bDeliver2 delivery-info" id="product-id-<?php echo $product->getId() ?>" data-shoplink="<?php echo $product->getLink().'/stock' ?>" data-calclink="<?php echo $page->url('product.delivery', array('productId' => $product->getId())) ?>">
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
    <div class="adfoxWrapper" id="<?php echo $adfox_id_by_label ?>"></div>
  </div>

    <?php echo $page->render('service/_listByProduct', array('product' => $product)) ?>
    <?php echo $page->render('warranty/_listByProduct', array('product' => $product)) ?>

</div>
<!-- /Goods info -->

<div class="clear"></div>

<!-- Photo video -->
<?php if (count($photo3dList) > 0 || count($photoList) > 0): ?>
<div class="fl width500">
  <h2>Фото товара:</h2>
  <div class="font11 gray pb10">Всего фотографий <?php echo count($photoList) ?></div>
  <ul class="previewlist">
    <?php foreach ($photoList as $photo): ?>
    <li class="viewstock" ref="photo<?php echo $photo->getId() ?>">
      <b>
        <a href="<?php echo $photo->getUrl(4) ?>" class="viewme" ref="image"></a>
      </b>
      <img src="<?php echo $photo->getUrl(2) ?>" alt="" width="48" height="48" />
    </li>
    <?php endforeach ?>
    <?php if (count($photo3dList) > 0): ?>
    <li><a href="#" class="axonometric viewme" ref="360" title="Объемное изображение">Объемное изображение</a></li>
    <?php endif ?>
  </ul>
</div>
<?php endif ?>
<!-- /Photo video -->

<?php if((bool)$product->getModel() && (bool)$product->getModel()->getProperty()): //модели ?>
<!-- Variation -->
<div class="fr width400">
    <h2>Этот товар с другими параметрами:</h2>
    <?php foreach ($product->getModel()->getProperty() as $property): ?>
        <?php if($property->getIsImage()): ?>
        <div class="bDropWrap">
            <h5><?php echo $property->getName() ?>:</h5>

            <ul class="previewlist">
                <?php foreach ($property->getOption() as $option): ?>
                <li>
                    <b<?php echo ($product->getId() == $option->getProduct()->getId()) ? ' class="current"' : '' ?> title="<?php echo $option->getHumanizedName() ?>"><a
                        href="<?php echo $option->getProduct()->getLink() ?>"></a></b>
                    <img src="<?php echo $option->getProduct()->getImageUrl(1) ?>" alt="<?php echo $option->getHumanizedName() ?>" width="48" height="48"/>
                </li>
                <?php endforeach ?>
            </ul>
        </div>

        <div class="clear"></div>
        <?php else: ?>
            <?php
                $productAttribute = $product->getPropertyById($property->getId());
                if (!$productAttribute) return;
            ?>
            <div class="bDropWrap">
                <h5><?php echo $property->getName() ?>:</h5>

                <div class="bDropMenu">
                    <span class="bold"><a href="<?php echo $product->getLink() ?>"><?php echo $productAttribute->getStringValue() ?></a></span>
                    <div>
                        <span class="bold"><a href="<?php echo $product->getLink() ?>"><?php echo $productAttribute->getStringValue() ?></a></span>

                <?php foreach ($property->getOption() as $option):?>
                    <?php if ($option->getValue() == $productAttribute->getValue())continue; ?>
                    <span>
                        <a href="<?php echo $option->getProduct()->getLink() ?>">
                            <?php echo $option->getHumanizedName() ?>
                        </a>
                    </span>
                <?php endforeach ?>
                    </div>

                </div>
            </div>
            <?php endif ?>
<?php endforeach; ?>
</div>
<!-- /Variation -->
<?php endif ?>


<?php if ($showAccessoryUpper && count($product->getAccessoryId()) && \App::config()->product['showAccessories']): ?>
    <?php echo $page->render('product/_slider', array('product' => $product, 'productList' => array_values($accessories), 'totalProducts' => count($product->getAccessoryId()), 'itemsInSlider' => \App::config()->product['itemsInSlider'], 'page' => 1, 'title' => 'Аксессуары', 'url' => $page->url('product.accessories', array('productToken' => $product->getToken())))) ?>
<?php endif ?>

<?php if ($showRelatedUpper && count($product->getRelatedId()) && \App::config()->product['showRelated']): ?>
    <?php echo $page->render('product/_slider', array('product' => $product, 'productList' => array_values($related), 'totalProducts' => count($product->getRelatedId()), 'itemsInSlider' => \App::config()->product['itemsInSlider'], 'page' => 1, 'title' => 'С этим товаром также покупают', 'url' => $page->url('product.related', array('productToken' => $product->getToken())))) ?>
<?php endif ?>

<?php if (false && \App::config()->smartEngine['pull']): ?>
<!--div class="clear"></div>
<div id="product_also_bought-container" data-url="<?php //echo url_for('smartengine_alsoBought', array('product' => $product->getId())) ?>" style="margin-top: 20px;"></div-->
<?php endif ?>

<?php if (\App::config()->smartEngine['pull']): ?>
<div class="clear"></div>
<div id="product_user-also_viewed-container" data-url="<?php echo $page->url('smartengine.pull.product_alsoViewed', array('productId' => $product->getId())) ?>" style="margin-top: 20px;"></div>
<?php endif ?>

<?php if (false && \App::config()->smartEngine['pull']): ?>
<!--div class="clear"></div>
<div id="product_user-recommendation-container" data-url="<?php //echo url_for('smartengine_userRecommendation', array('product' => $product->getId())) ?>" style="margin-top: 20px;"><h3>Recommendations for user...</h3></div-->
<?php endif ?>

<?php $description = $product->getDescription(); ?>
<?php if (!empty($description)): ?>
<!-- Information -->
<div class="clear"></div>
<h2 class="bold"><?php echo $product->getName() ?> - Информация о товаре</h2>
<div class="line pb15"></div>
<ul class="pb10">
  <?php echo $description ?>
</ul>
<!-- /Information  -->

<?php endif ?>
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
      <div class="pb5"><?php echo $product->getName() ?></div>
      <div class="pb5">
          <strong class="font34"><span class="price"><?php echo $page->helper->formatPrice($product->getPrice()) ?></span> <span class="rubl">p</span></strong>
      </div>
      <div class="goodsbarbig mSmallBtns pb40" ref="<?php echo $product->getToken() ?>" data-value='<?php echo $json ?>'>
        <?php echo $page->render('cart/_button', array('product' => $product, 'disabled' => !$product->getIsBuyable())) ?>
      </div>

      <h2>Фото:</h2>
      <ul class="previewlist">
        <?php foreach ($photoList as $photo): ?>
        <li class="viewstock" ref="photo<?php echo $photo->getId() ?>"><b><a href="<?php echo $photo->getUrl(4) ?>" class="viewme" ref="image" id="try-3"></a></b><img src="<?php echo $photo->getUrl(2) ?>" alt="" width="48" height="48" /></li>
        <?php endforeach ?>
        <?php if (count($photo3dList) > 0): ?>
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



<div id="order1click-container" class="bMobDown mBR5 mW2 mW900" style="display: none">
  <div class="bMobDown__eWrap">
    <div class="bMobDown__eClose close"></div>
    <h2>Покупка в 1 клик!</h2>
    <div class="clear line pb20"></div>

    <form id="order1click-form" action="<?php echo $page->url('order.1click', array('product' => $product->getBarcode())) ?>" method="post"></form>

  </div>
</div>

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

<?php if (2 == $product->getViewId() && count($product->getKit())): ?>
    <h2 class="bold fl"><?php echo $product->getName() ?> включает в себя:</h2>

    <div class="line"></div>
    <div style="width: 940px; float: none; margin: 0;" class="goodslist">
        <?php $i = 0; foreach ($product->getKit() as $part): $i++ ?>
        <?php echo $page->render('product/show/_compact', array('product' => $kit[$part->getId()], 'kit' => $part)) ?>
        <?php if (0 == ($i % 4)): ?><br class="clear" /><?php endif ?>
        <?php endforeach ?>

    </div>

<div class="clear pb25"></div>
<?php endif ?>

<h2 class="bold"><?php echo $product->getName() ?> - Характеристики</h2>
<div class="line pb25"></div>
<div class="descriptionlist">

    <?php foreach ($product->getGroupedProperties() as $group): ?>
    <?php if (!count($group['properties'])): continue; endif ;?>
    <div class="pb15"><strong><?php echo $group['group']->getName() ?></strong></div>
    <?php foreach ($group['properties'] as $property): ?>
        <div class="point">
            <div class="title"><h3><?php echo $property->getName() ?></h3></div>
            <div class="description">
                <?php echo $property->getStringValue() ?>
            </div>
        </div>
        <?php endforeach ?>
    <?php endforeach ?>

</div>

<?php if (count($product->getTag())): ?>
<noindex>
    <div class="pb25">
        <strong>Теги:</strong>
<?php foreach ($product->getTag() as $i => $tag):?>
<?php echo ($i ? ', ' : '').'<a href="'.$page->url('tag', array('tagToken' => $tag->getToken())).'" class="underline" rel="nofollow">'.$tag->getName().'</a>' ?>
<?php endforeach ?>
    </div>
</noindex>
<?php endif ?>

<?php if (!$showAccessoryUpper && count($product->getAccessoryId()) && \App::config()->product['showAccessories']): ?>
    <?php echo $page->render('product/_slider', array('product' => $product, 'productList' => array_values($accessories), 'totalProducts' => count($product->getAccessoryId()), 'itemsInSlider' => \App::config()->product['itemsInSlider'], 'page' => 1, 'title' => 'Аксессуары', 'url' => $page->url('product.accessories', array('productToken' => $product->getToken())))) ?>
<?php endif ?>

<?php if (!$showRelatedUpper && count($product->getRelatedId()) && \App::config()->product['showRelated']): ?>
    <?php echo $page->render('product/_slider', array('product' => $product, 'productList' => array_values($related), 'totalProducts' => count($product->getRelatedId()), 'itemsInSlider' => \App::config()->product['itemsInSlider'], 'page' => 1, 'title' => 'С этим товаром также покупают', 'url' => $page->url('product.related', array('productToken' => $product->getToken())))) ?>
<?php endif ?>

<div class="line"></div>
<div class="fr ar">
    <?php if ( $product->getIsBuyable()): ?>
    <div class="goodsbarbig mSmallBtns" ref="<?php echo $product->getToken() ?>" data-value='<?php echo $json ?>'>

        <div class='bCountSet'>
            <?php if (!$user->getCart()->hasProduct($product->getId())): ?>
            <a class='bCountSet__eP' href>+</a><a class='bCountSet__eM' href>-</a>
            <?php else: ?>
            <a class='bCountSet__eP disabled' href>&nbsp;</a><a class='bCountSet__eM disabled' href>&nbsp;</a>
            <?php endif ?>
            <span><?php echo $user->getCart()->getQuantityByProduct($product->getId()) ? $user->getCart()->getQuantityByProduct($product->getId()) : 1 ?> шт.</span>
        </div>

        <?php echo $page->render('cart/_button', array('product' => $product, 'disabled' => !$product->getIsBuyable())) ?>
    </div>
    <?php else: ?>
    <p class="font16 orange">Для покупки товара<br />обратитесь в<br />Контакт-сENTER</p>
    <?php endif ?>
</div>
<div class="fr mBuyButtonBottom">
    <div class="pb10"><strong class="font34"><span class="price"><?php echo $page->helper->formatPrice($product->getPrice()) ?></span> <span class="rubl">p</span></strong></div>
</div>
<div class="fl mBuyButtonBottom onleft" >
    <h2 class="bold"><?php echo $product->getName() ?></h2>
</div>


<br class="clear" />

<?php if ($showAveragePrice): ?>
    <div class="gray pt20 mb10">*по данным мониторинга компании Enter</div>
    <div class="clear"></div>
<?php endif ?>

<?php echo $page->render('_breadcrumbs', array('breadcrumbs' => $breadcrumbs, 'class' => 'breadcrumbs-footer')) ?>

<?php if (\App::config()->smartEngine['push']): ?>
<div id="product_view-container" data-url="<?php echo $page->url('smartengine.push.product_view', array('productId' => $product->getId())) ?>"></div>
<?php endif ?>

<?php if ($product->getIsBuyable()): echo $page->render('product/form-oneClick'); endif; ?>
