<?php
/**
 * @var $view
 * @var $item ProductEntity
 * @var $ii
 * @var $maxPerPage
 * @var $relatedPagesNum int
 * @var $accessoryPagesNum int
 * @var $showRelatedUpper boolean
 * @var $showAccessoryUpper boolean
 */
$json = json_encode(array (
  'jsref' => $item->getToken(),
  'jstitle' => htmlspecialchars($item->getName(), ENT_QUOTES, 'UTF-8'),
  'jsprice' => $item->getPrice(),
  'jsimg' => $item->getMediaImageUrl(3),
  'jsbimg' =>  $item->getMediaImageUrl(2),
  'jsshortcut' =>  $item->getArticle(),
  'jsitemid' =>  $item->getId(),
  'jsregionid' => sfContext::getInstance()->getUser()->getRegionCoreId(),
  'jsregionName' => sfContext::getInstance()->getUser()->getRegion('name'),
  'jsstock' => 10,
));
?>
<?php
$photoList=$item->getPhotoList();
$photo3dList=$item->getPhoto3dList();
$p3d_res_small = array();
$p3d_res_big = array();
foreach ($photo3dList as $photo3d)
{
  $p3d_res_small[] = $photo3d->getUrl(0);
  $p3d_res_big[] = $photo3d->getUrl(1);
}
?>
<script type="text/javascript">
  product_3d_small = <?php echo json_encode($p3d_res_small) ?>;
  product_3d_big = <?php echo json_encode($p3d_res_big) ?>;
</script>

<?php slot('after_body_block') ?>
<?php render_partial('product_/templates/_oneclickTemplate.php', array()) ?>
<?php end_slot() ?>

<div class="goodsphoto">
  <a href="<?php echo $item->getMediaImageUrl(4)  ?>" class="viewme" ref="image" onclick="return false">
    <?php foreach ($item->getLabelList() as $label):?>
    <img class="bLabels" src="<?php echo $label->getImageUrl(1) ?>" alt="<?php echo $label->getName() ?>" />
    <?php endforeach ?>
    <img src="<?php echo $item->getMediaImageUrl(3) ?>" alt="" width="500" height="500" title="" />
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
      <span id="rating" data-url="<?php echo url_for('userProductRating_createtotal', array('rating' => 'score', 'product' => $item->getId() )) ?>">
        <?php
        echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($item->getRating()));
        echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($item->getRating()));
        ?>
      </span>
      <strong class="ml5 hf"><?php echo round($item->getRating(), 1) ?></strong>
      <a href="<?php echo url_for('productComment', array('product' => $item->getPath())) ?>" class="underline ml5">Читать отзывы</a> <span>(<?php echo $item->getCommentCount() ?>)</span>
    </div>
    <span>Артикул #<?php echo $item->getArticle() ?></span>
  </div>

  <div class="font14 pb15"><?php echo $item->getTagline() ?></div>
  <div class="clear"></div>

<?php if($item->haveToShowOldPrice()): ?>
  <div style="text-decoration: line-through; font: normal 18px verdana; letter-spacing: -0.05em; color: #6a6a6a;"><?php render_partial('product_/templates/_price.php', array('price' => formatPrice($item->getPriceOld()), 'noStrong' => true, )) ?></div>
  <?php elseif($item->haveToShowAveragePrice()): ?>
  <div class="mOurGray">
    Средняя цена в магазинах города*<br><div class='mOurGray mIco'><?php render_partial('product_/templates/_price.php', array('price' => $item->getPriceAverage(), 'noStrong' => true, )) ?> &nbsp;</div>
  </div>
  <?php slot('additional_data') ?>
  <div class="gray pt20 mb10">*по данным мониторинга компании Enter</div>
  <div class="clear"></div>
  <?php end_slot() ?>
  <div class="clear"></div>
  <div class="clear mOur pt10 <?php if ($item->hasSaleLabel()) echo 'red'; ?>">Наша цена</div>
  <?php endif ?>

  <div class="fl pb15">
    <div class="pb10 <?php if ($item->hasSaleLabel()) echo 'red'; ?>"><?php render_partial('product_/templates/_price.php', array('price' => formatPrice($item->getPrice()))) ?></div>
    <?php if ($item->getIsBuyable()): ?>
    <div class="pb5"><strong class="orange">Есть в наличии</strong></div>
    <?php endif ?>
  </div>


  <div class="fr ar pb15">
    <div class="goodsbarbig mSmallBtns" ref="<?php echo $item->getToken() ?>" data-value='<?php echo $json ?>'>

      <div class='bCountSet'>
        <?php if (!$item->isInCart()): ?>
        <a class='bCountSet__eP' href>+</a><a class='bCountSet__eM' href>-</a>
        <?php else: ?>
        <a class='bCountSet__eP disabled' href>&nbsp;</a><a class='bCountSet__eM disabled' href>&nbsp;</a>
        <?php endif ?>
        <span><?php echo $item->isInCart() ? $item->getCartQuantity() : 1 ?> шт.</span>
      </div>

      <?php render_partial('cart_/templates/_buy_button.php', array('item' => $item)) ?>
    </div>
    <?php if ( $item->getState()->getIsBuyable()): ?>
      <div class="pb5"><strong>
        <a href=""
          data-model='<?php echo $json ?>'
          link-output='<?php echo url_for('order_1click', array('product' => $item->getBarcode())) ?>'
          link-input='<?php echo url_for('product_delivery_1click') ?>'
          class="red underline order1click-link-new">Купить быстро в 1 клик</a>
      </strong></div>
    <?php endif ?>
  </div>


  <div class="line pb15"></div>


  <?php if ($item->getIsBuyable()): ?>
  <div class="bDeliver2 delivery-info" id="product-id-<?php echo $item->getId() ?>" data-shoplink="<?php echo url_for('productStock', array('product' => $item->getPath())) ?>" data-calclink="<?php echo url_for('product_delivery', array('product' => $item->getId())) ?>">
    <h4>Как получить заказ?</h4>
    <ul>
      <li>
        <h5>Идет расчет условий доставки...</h5>
      </li>
    </ul>
  </div>
  <?php endif ?>

  <div class="pb15">
    <a href="http://clck.yandex.ru/redir/dtype=stred/pid=47/cid=1248/*http://market.yandex.ru/grade-shop.xml?shop_id=83048"><img src="http://clck.yandex.ru/redir/dtype=stred/pid=47/cid=1248/*http://img.yandex.ru/market/informer12.png" border="0" alt="Оцените качество магазина на Яндекс.Маркете." /></a>
  </div>
  <div class="line pb15"></div>

  <div style="margin-bottom: 20px;">
    <?php
    //если стоит шильдик Акция
    $labels = $item->getLabelList();
    $label = isset($labels[0])? $labels[0] : null;
    if ($label && $label->getId() == ProductLabelEntity::LABEL_ACTION) { ?>
      <div class="adfoxWrapper" id="adfox400counter"></div>
    <?php } else if ($label && $label->getId() == ProductLabelEntity::LABEL_CREDIT) { ?>
      <div class="adfoxWrapper" id="adfoxWowCredit"></div>
    <?php } else { ?>
      <div class="adfoxWrapper" id="adfox400"></div>
    <?php } ?>
  </div>

  <?php render_partial('service_/templates/_listByProduct.php', array('item' => $item)) ?>

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

<?php render_partial('product_/templates/_product_model.php', array('item' => $item)) ?>

<div class="clear"></div>

<?php
if ($showAccessoryUpper && count($item->getAccessoryList())){
  render_partial('product_/templates/_product_accessory.php', array(
    'product' => $item,
    'accessoryPagesNum' => $accessoryPagesNum,
  ));
}

if ($showRelatedUpper && count($item->getRelatedList())){
  render_partial('product_/templates/_product_related.php', array(
    'item' => $item,
    'relatedPagesNum' => $relatedPagesNum,
  ));
}
?>

<?php $description = $item->getDescription(); ?>
<?php if (!empty($description)): ?>
<!-- Information -->
<h2 class="bold"><?php echo $item->getName() ?> - Информация о товаре</h2>
<div class="line pb15"></div>
<ul class="pb10">
  <?php echo $description ?>
</ul>
<!-- /Information  -->
<div class="clear"></div>
<?php endif ?>

<?php if ('kit' != $item->getView()): ?>
<!-- Description -->
<h2 class="bold"><?php echo $item->getName() ?> - Характеристики</h2>
<div class="line pb25"></div>


<div class="descriptionlist">
  <?php render_partial('product_/templates/_property_grouped.php', array('item' => $item)) ?>
</div>

<!-- /Description -->
<?php render_partial('product_/templates/_tags.php', array('item' => $item)) ?>
<?php endif ?>

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
      <div class="pb5"><?php echo $item->getName() ?></div>
      <div class="pb5">
        <?php render_partial('product_/templates/_price.php', array('price' => formatPrice($item->getPrice()) )) ?>
      </div>
      <div class="popup_leftpanel pb40" ref="<?php echo $item->getToken() ?>" data-value='<?php echo $json ?>'>
        <?php render_partial('cart_/templates/_buy_button.php', array('item' => $item, 'text' => 'Купить')) ?>
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

    <form id="order1click-form" action="<?php echo url_for('order_1click', array('product' => $item->getBarcode())) ?>" method="post"></form>

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
