<?php foreach (array('id', 'name', 'latitude', 'longitude') as $i): ?>
  <input type="hidden" name="shop[<?php echo $i ?>]" value="<?php echo $order[$i] ?>" />
<?php endforeach ?>


<!-- bMap -->
<div class="bMap">
  <div id="map-container" class='bMap__eBody'></div>

  <div class='bMap__eImages'>
    <h3 class='bMap__eImagesTitle'>Смотри как внутри</h3>

    <div class='bMap__eScrollWrap'>

      <?php $i = 0; $count = count($order['photos']); foreach ($order['photos'] as $photo): $i++ ?>
      <div class="bMap__eContainer<?php if (1 == $i) echo ' first' ?> map-image-link">
        <img class="bMap__eImgSmall" src="<?php echo $photo['url_small'] ?>" />
        <div class='bMap__eImgBig'><img src="<?php echo $photo['url_big'] ?>"></div>

        <?php if ((1 == $i) && !empty($shop->panorama)): ?>
        <div class="bMap__e360 map-360-link"></div>
        <?php endif ?>
      </div>
      <?php endforeach ?>

      <div class="bMap__eContainer map-google-link">
        <img class='bMap__eImgSmall' src='/images/map_ico.png'>
        <div class='bMap__eImgBig'></div>
      </div>

    </div>
  </div>

  <input id="map-panorama" type="hidden" data-swf="<?php echo $order['panorama']['swf'] ?>" data-xml="<?php echo $order['panorama']['xml'] ?>" />
</div>
<!-- /bMap -->

<!-- bMapInfo -->
<div class='bMapInfo'>
  <div class='bMapInfo__eIco mShop'></div>
  <div class='bMapInfo__eText'>
    <h2 class='bMapInfo__eTitle'><?php echo $order['address'] ?></h2>
    <p class='bMapInfo__eP'>
      <span class='bMapInfo__eSpan mBig'>

        <?php if($order['is_reconstruction']): ?>
        <span class="red">На реконструкции</span><br />
        <?php elseif($order['regime']): ?>
        Работаем <?php echo $order['regime'] ?><br />
        <?php endif ?>
        <?php if ($order['phonenumbers']): ?>
        Телефон<?php echo false !== strpos($order['phonenumbers'], ',') ? 'ы' : '' ?>: <?php echo $order['phonenumbers'] ?><br />
        <?php endif ?>
      </span>
    </p>
    <p class='bMapInfo__eP'>
      <span class='bMapInfo__eSpan mSmall'>
        <?php echo $order['description'] ?>
      </span>
    </p>

  </div>
</div>
<!-- /bMapInfo -->

<!-- bMapInfo -->
<div class='bMapInfo'>
  <div class='bMapInfo__eIco mCar'></div>
  <div class='bMapInfo__eText'>
    <h2 class='bMapInfo__eTitle'>Как добраться</h2>

    <?php if ($order['way_walk']): ?>
    <p class='bMapInfo__eP'>
      <span class='bMapInfo__eSpan mSmall'>
        <b>Проезд на городском транспорте</b><br>
        <?php echo $order['way_walk'] ?>
      </span>
    </p>
    <?php endif ?>

    <?php if ($order['way_auto']): ?>
    <p class='bMapInfo__eP'>
      <span class='bMapInfo__eSpan mSmall'>

        <b>Проезд на авто</b><br>
        <?php echo $order['way_auto'] ?>
      </span>
    </p>
    <?php endif ?>

  </div>
</div>
<!-- /bMapInfo -->

<div class="clear"></div>

