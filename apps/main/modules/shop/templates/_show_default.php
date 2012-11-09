<?php foreach (array('id', 'name', 'latitude', 'longitude') as $i): ?>
  <input type="hidden" name="shop[<?php echo $i ?>]" value="<?php echo $item[$i] ?>" />
<?php endforeach ?>


<!-- bMap -->
<div class="bMap">
  <div id="map-container" class='bMap__eBody'></div>

  <div class='bMap__eImages'>
    <h3 class='bMap__eImagesTitle'>Смотри как внутри</h3>

    <div class='bMap__eScrollWrap'>

      <?php $i = 0; $count = count($item['photos']); foreach ($item['photos'] as $photo): $i++ ?>
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

  <input id="map-panorama" type="hidden" data-swf="<?php echo $item['panorama']['swf'] ?>" data-xml="<?php echo $item['panorama']['xml'] ?>" />
</div>
<!-- /bMap -->
<a class="printPage__printAction bBigOrangeButton" href="javascript:window.print()">Распечатать</a>
<!-- bMapInfo -->
<div class='bMapInfo'>
  <div class='bMapInfo__eIco mShop'></div>
  <div class='bMapInfo__eText'>
    <h2 class='bMapInfo__eTitle'><?php echo $item['address'] ?></h2>
    <p class='bMapInfo__eP'>
      <span class='bMapInfo__eSpan mBig'>

        <?php if($item['is_reconstruction']): ?>
        <span class="red">На реконструкции</span><br />
        <?php elseif($item['regime']): ?>
        Работаем <?php echo $item['regime'] ?><br />
        <?php endif ?>
        <?php if ($item['phonenumbers']): ?>
        Телефон<?php echo false !== strpos($item['phonenumbers'], ',') ? 'ы' : '' ?>: <?php echo $item['phonenumbers'] ?><br />
        <?php endif ?>
      </span>
    </p>
    <p class='bMapInfo__eP'>
      <span class='bMapInfo__eSpan mSmall'>
        <?php echo $item['description'] ?>
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

    <?php if ($item['way_walk']): ?>
    <p class='bMapInfo__eP'>
      <span class='bMapInfo__eSpan mSmall'>
        <b>Проезд на городском транспорте</b><br>
        <?php echo $item['way_walk'] ?>
      </span>
    </p>
    <?php endif ?>

    <?php if ($item['way_auto']): ?>
    <p class='bMapInfo__eP'>
      <span class='bMapInfo__eSpan mSmall'>

        <b>Проезд на авто</b><br>
        <?php echo $item['way_auto'] ?>
      </span>
    </p>
    <?php endif ?>

  </div>
</div>
<!-- /bMapInfo -->

<div class="clear"></div>

