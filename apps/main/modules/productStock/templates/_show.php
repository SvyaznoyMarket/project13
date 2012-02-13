<input id="map-center" type="hidden" data-content='<?php echo json_encode(array('latitude' => $region['latitude'], 'longitude' => $region['longitude'])) ?>' />
<input id="map-markers" type="hidden" data-content='<?php echo json_encode($markers) ?>' />
<div id="map-info_window-container" class="hf">
  <div class="bigmark">
    <b class="corner"></b>
    <div>
      <h2 class="title" data-name="name"></h2>
      <span class="shopnum" data-name="url" style="display:none"></span>
	<br>
	<br>
	<input class="button whitebutton shopchoose" type="button" value="Купить в этом магазине">
    </div>
  </div>
</div>


<!-- shop list -->
<div class='bInShop'>
  <div class='bInShop__eLeft'>
    <div class='bInShop__eMap' id='gMap'></div>
    <div class='bInShop__eCurrent'><h2><?php echo ($region['prefix'] ? ($region['prefix'].' ') : '').$region['name'] ?></h2> <a href class='bGrayButton'>Другой город</a></div>

    <?php if (count($shopList)): ?>
      <div class='bInShopLine mInF'>
        <div class='bInShopLine__eTitle'>Адреса магазинов</div>
        <div class='bInShopLine__eCount mInF'>Наличие</div>

        <div class='bInShopLine__eButton'></div>
      </div>

      <?php foreach ($shopList as $shop): ?>
      <div class='bInShopLine'>
        <div class='bInShopLine__eTitle'><a href style='font: 14px Tahoma, sans-srif'><?php echo $shop['name'] ?></a></div>
        <div class='bInShopLine__eCount'><?php include_partial('productStock/quantity', array('quantity' => $shop['product_quantity'])) ?></div>
        <div class='bInShopLine__eButton'><a href="<?php echo url_for('order_1click', array('product' => $product['barcode'], 'shop' => $shop['token'])) ?>" class='bGrayButton'>Купить в этом магазине</a></div>
      </div>
      <?php endforeach ?>
    <?php endif ?>

  </div>

  <div class='bInShop__eRight'>
    <?php include_component('product', 'show', array('product' => $product, 'view' => 'stock')) ?>
  </div>
</div>
<!-- /shop list -->


<div id="order1click-container" class="bMobDown mBR5 mW2 mW900" style="display: none">
  <div class="bMobDown__eWrap">
    <div class="bMobDown__eClose close"></div>
    <h2>Покупка в 1 клик!</h2>
    <div class="clear line pb20"></div>

    <form id="order1click-form" action="<?php echo url_for('order_1click', array('product' => $product['barcode'])) ?>" method="post"></form>

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